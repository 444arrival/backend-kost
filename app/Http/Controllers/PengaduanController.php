<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;

class PengaduanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user() && $request->user()->role === 'admin') {
            $pengaduans = Pengaduan::with('user')->latest()->get();
        } else {
            $pengaduans = Pengaduan::where('user_id', $request->user()->id)->latest()->get();
        }

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
            'status' => 'Menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil dikirim',
            'data' => $pengaduan
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $pengaduan = Pengaduan::find($id);

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        $pengaduan->status = $request->status;
        $pengaduan->save();

        return response()->json([
            'message' => 'Status pengaduan berhasil diperbarui',
            'data' => $pengaduan
        ]);
    }
}