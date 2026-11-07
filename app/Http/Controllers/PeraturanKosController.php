<?php

namespace App\Http\Controllers;

use App\Models\PeraturanKos;
use Illuminate\Http\Request;

class PeraturanKosController extends Controller
{
    public function index()
    {
        $peraturan = PeraturanKos::all();
        return response()->json([
            'status' => true,
            'data' => $peraturan
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_poin' => 'required|string',
        ]);

        $peraturan = PeraturanKos::create([
            'judul' => $request->judul,
            'isi_poin' => $request->isi_poin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Peraturan berhasil ditambahkan',
            'data' => $peraturan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_poin' => 'required|string',
        ]);

        $peraturan = PeraturanKos::find($id);

        if (!$peraturan) {
            return response()->json([
                'status' => false,
                'message' => 'Peraturan tidak ditemukan'
            ], 404);
        }

        $peraturan->update([
            'judul' => $request->judul,
            'isi_poin' => $request->isi_poin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Peraturan berhasil diperbarui',
            'data' => $peraturan
        ], 200);
    }

    public function destroy($id)
    {
        $peraturan = PeraturanKos::find($id);

        if (!$peraturan) {
            return response()->json([
                'status' => false,
                'message' => 'Peraturan tidak ditemukan'
            ], 404);
        }

        $peraturan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Peraturan berhasil dihapus'
        ], 200);
    }
}