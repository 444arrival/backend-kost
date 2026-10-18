<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumuman;

class PengumumanController extends Controller {
    public function index() {
        return response()->json(Pengumuman::latest()->get());
    }

    public function store(Request $request)
{
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
}