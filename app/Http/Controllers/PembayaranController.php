<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    // Metode pilihan di aplikasi -> daftar metode yang ditampilkan Snap.
    // Kalau Snap kosong/error untuk satu metode, metode itu belum aktif di akun Midtrans
    // (cek Settings > Snap Preferences / Payment). Hapus 'enabled_payments' untuk menampilkan semua.
    private function enabledPayments(?string $metode): ?array
    {
        return match ($metode) {
            'qris'          => ['other_qris'],
            'transfer_bank' => ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'],
            'ewallet'       => ['gopay', 'shopeepay', 'dana'],
            default         => null,
        };
    }

    // GET /api/tagihan-aktif -> tagihan belum lunas milik user yang login
    public function tagihanAktif(Request $request)
    {
        $tagihan = Tagihan::where('user_id', $request->user()->id)
            ->where('status', '!=', 'lunas')
            ->orderBy('jatuh_tempo')
            ->first();

        return response()->json(['data' => $tagihan]);
    }

    // GET /api/riwayat-pembayaran -> tagihan yang sudah lunas
    public function riwayat(Request $request)
    {
        $data = Tagihan::where('user_id', $request->user()->id)
            ->where('status', 'lunas')
            ->orderByDesc('jatuh_tempo')
            ->limit(20)
            ->get(['id', 'periode', 'nominal', 'jatuh_tempo', 'dibayar_pada']);

        return response()->json(['data' => $data]);
    }

    // POST /api/pembayaran/snap  { "tagihan_id": 1, "metode": "qris" }
    public function snap(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|integer',
            'metode'     => 'nullable|in:qris,transfer_bank,ewallet',
        ]);

        $user    = $request->user();
        $tagihan = Tagihan::where('id', $request->tagihan_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($tagihan->status === 'lunas') {
            return response()->json(['message' => 'Tagihan sudah lunas'], 422);
        }

        // order_id harus unik tiap percobaan
        $orderId = 'SEWA-' . $tagihan->id . '-' . time();
        $tagihan->update(['order_id' => $orderId, 'status' => 'menunggu']);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $tagihan->nominal,
            ],
            'item_details' => [[
                'id'       => 'TAGIHAN-' . $tagihan->id,
                'price'    => (int) $tagihan->nominal,
                'quantity' => 1,
                'name'     => 'Sewa kamar ' . $tagihan->kamar . ' - ' . $tagihan->periode,
            ]],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'callbacks' => [
                'finish' => url('/api/payment/finish'),
            ],
        ];

        $enabled = $this->enabledPayments($request->metode);
        if ($enabled) {
            $params['enabled_payments'] = $enabled;
        }

        $snap = Snap::createTransaction($params);

        return response()->json([
            'snap_token'   => $snap->token,
            'redirect_url' => $snap->redirect_url,
            'order_id'     => $orderId,
        ]);
    }

    // POST /api/pembayaran/notifikasi  (dipanggil server Midtrans, tanpa login)
    public function notifikasi(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');

        // verifikasi signature supaya tidak bisa dipalsukan
        $signature = hash(
            'sha512',
            $request->order_id . $request->status_code . $request->gross_amount . $serverKey
        );
        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        $tagihan = Tagihan::where('order_id', $request->order_id)->first();
        if (! $tagihan) {
            return response()->json(['message' => 'Tagihan tidak ditemukan'], 404);
        }

        $status = $request->transaction_status;
        $fraud  = $request->fraud_status;

        if ($status === 'capture') {
            $tagihan->status = $fraud === 'accept' ? 'lunas' : 'menunggu';
        } elseif ($status === 'settlement') {
            $tagihan->status = 'lunas';
        } elseif ($status === 'pending') {
            $tagihan->status = 'menunggu';
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $tagihan->status = 'gagal';
        }

        // catat waktu pembayaran sekali saja
        if ($tagihan->status === 'lunas' && $tagihan->dibayar_pada === null) {
            $tagihan->dibayar_pada = now();
        }

        $tagihan->save();

        return response()->json(['message' => 'OK']);
    }
}