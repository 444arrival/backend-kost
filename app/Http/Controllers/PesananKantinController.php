<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesananKantin;
use App\Models\DetailPesananKantin;

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
}