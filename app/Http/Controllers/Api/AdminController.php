<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function getPenghuni()
    {

        $penghuni = User::where('role', 'penghuni')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengambil data penghuni',
            'data'    => $penghuni
        ], 200);
    }

    public function tambahPenghuni(Request $request)
    {

        $request->validate([
            'nama_lengkap' => 'required|string',
            'username'     => 'required|string|unique:users,username',
            'password'     => 'required|string|min:6',
            'no_hp'        => 'required|string',
            'nomor_kamar'  => 'required|string',
        ]);

        $penghuni = User::create([
            'name'        => $request->nama_lengkap,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'no_hp'       => $request->no_hp,
            'nomor_kamar' => $request->nomor_kamar,
            'role'        => 'penghuni',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Akun penghuni berhasil dibuat!',
            'data'    => $penghuni
        ], 201);
    }
}