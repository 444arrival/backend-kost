<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        // Jika user yang login adalah admin, tampilkan semua pengaduan beserta data user/pelapornya
        if ($request->user() && $request->user()->role === 'admin') {
            $pengaduans = Pengaduan::with('user')->latest()->get();
        } else {
            // Jika penghuni biasa, tampilkan pengaduan miliknya sendiri
            $pengaduans = Pengaduan::where('user_id', $request->user()->id)->latest()->get();
        }

        // Kembalikan LANGSUNG berupa Array/List JSON (tanpa dibungkus 'success' / 'data')
        return response()->json($pengaduans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required',
            'judul' => 'required',
            'deskripsi' => 'required',
        ]);

        $pengaduan = Pengaduan::create([
            'user_id' => $request->user()->id,
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => 'Menunggu', // Status awal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dikirim',
            'data' => $pengaduan
        ], 201);
    }
}