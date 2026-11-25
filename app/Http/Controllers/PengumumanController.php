<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumuman;

class PengumumanController extends Controller {
    public function index() {
        return response()->json(Pengumuman::latest()->get());
    }

    public function store(Request $request) {
        $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
        ]);

        $pengumuman = Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dibuat',
            'data' => $pengumuman
        ], 201);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'judul' => 'required|string',
            'isi' => 'required|string',
        ]);

        $pengumuman = Pengumuman::find($id);
        if (!$pengumuman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui',
            'data' => $pengumuman
        ], 200);
    }

    public function destroy($id) {
        $pengumuman = Pengumuman::find($id);
        if (!$pengumuman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus'
        ], 200);
    }
}