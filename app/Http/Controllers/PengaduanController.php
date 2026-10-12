<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil pengaduan berdasarkan user yang sedang login (menggunakan Sanctum)
        $pengaduans = Pengaduan::where('user_id', $request->user()->id)->latest()->get();
        return response()->json(['success' => true, 'data' => $pengaduans]);
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