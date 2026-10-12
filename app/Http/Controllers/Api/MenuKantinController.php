<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuKantin;
use Illuminate\Http\Request;

class MenuKantinController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => MenuKantin::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'harga' => 'required|string',
            'kategori' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('menu', 'public');
        }

        $menu = MenuKantin::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'kategori' => $request->kategori,
            'foto' => $path ? asset('storage/' . $path) : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ], 201);
    }

    public function destroy($id)
    {
        $menu = MenuKantin::find($id);
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan'], 404);
        }

        $menu->delete();
        return response()->json(['success' => true, 'message' => 'Menu berhasil dihapus'], 200);
    }

    public function update(Request $request, $id)
{
    $menu = MenuKantin::find($id);
    if (!$menu) {
        return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan'], 404);
    }

    $request->validate([
        'nama' => 'required|string',
        'harga' => 'required|string',
        'kategori' => 'required|string',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $path = $menu->foto;
    if ($request->hasFile('foto')) {
        $path = asset('storage/' . $request->file('foto')->store('menu', 'public'));
    }

    $menu->update([
        'nama' => $request->nama,
        'harga' => $request->harga,
        'kategori' => $request->kategori,
        'foto' => $path
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Menu berhasil diperbarui',
        'data' => $menu
    ], 200);
}
}