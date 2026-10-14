<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PesananKantin;
use App\Models\DetailPesananKantin;
use Midtrans\Config;
use Midtrans\Snap;

class PesananKantinController extends Controller
{
    public function index()
    {
        $pesanan = PesananKantin::with('details')->latest()->get();

        $data = $pesanan->map(function($p) {
            $ringkasan = $p->details->map(function($d) {
                return "{$d->nama_menu} ({$d->quantity}x)";
            })->implode(', ');

            return [
                'id' => $p->id,
                'nama_penghuni' => $p->nama_penghuni,
                'nomor_kamar' => $p->nomor_kamar,
                'item_pesanan' => $ringkasan,
                'total_harga' => 'Rp ' . number_format($p->total_harga, 0, ',', '.'),
                'catatan' => $p->catatan,
                'status' => $p->status
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    public function store(Request $request)
    {
        $pesanan = PesananKantin::create([
            'nama_penghuni' => $request->nama_penghuni,
            'nomor_kamar' => $request->nomor_kamar,
            'total_harga' => $request->total_harga,
            'catatan' => $request->catatan,
            'status' => 'Diproses'
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                DetailPesananKantin::create([
                    'pesanan_kantin_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'nama_menu' => $item['nama_menu'],
                    'quantity' => $item['quantity'],
                    'harga' => $item['harga']
                ]);
            }
        }

        return response()->json([
            'message' => 'Pesanan berhasil dibuat!',
            'data' => $pesanan
        ], 201);
    }

    public function checkout(Request $request)
    {
        Log::info('DATA CHECKOUT KANTIN MASUK:', $request->all());
        Log::info('DATABASE AKTIF: ' . \DB::connection()->getDatabaseName());

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        $orderId = 'KANTIN-' . time();
        
        $totalHargaBersih = (int) preg_replace('/[^0-9]/', '', $request->total_harga);

        $pesanan = PesananKantin::create([
            'nama_penghuni' => $request->nama_penghuni,
            'nomor_kamar' => $request->nomor_kamar,
            'total_harga' => $totalHargaBersih,
            'catatan' => $request->catatan ?? '-',
            'status' => 'Diproses'
        ]);

        Log::info('PESANAN BERHASIL DISIMPAN DENGAN ID: ' . $pesanan->id);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $hargaItemBersih = (int) preg_replace('/[^0-9]/', '', $item['harga']);

                DetailPesananKantin::create([
                    'pesanan_kantin_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'nama_menu' => $item['nama_menu'],
                    'quantity' => $item['quantity'],
                    'harga' => $hargaItemBersih
                ]);
            }
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalHargaBersih,
            ],
            'customer_details' => [
                'first_name' => $request->nama_penghuni,
                'notes' => 'Kamar ' . $request->nomor_kamar,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ], 200);
        } catch (\Exception $e) {
            Log::error('MIDTRANS ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $pesanan = PesananKantin::find($id);
        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $pesanan->status = $request->status;
        $pesanan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'data' => $pesanan
        ], 200);
    }
}