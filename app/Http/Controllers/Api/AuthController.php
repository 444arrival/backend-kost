<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role'     => 'required|in:admin,penghuni'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Username atau Password salah!'
            ], 401);
        }

        if ($user->role !== $request->role) {
            return response()->json([
                'status'  => false,
                'message' => 'Akun ini tidak terdaftar sebagai ' . ucfirst($request->role)
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login Berhasil',
            'token'   => $token,
            'data'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'username'    => $user->username,
                'nomor_kamar' => $user->nomor_kamar,
                'role'        => $user->role
            ]
        ], 200);
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        if ($user->foto && \Illuminate\Support\Facades\Storage::exists('public/' . $user->foto)) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $user->foto);
        }

        $path = $request->file('foto')->store('profil', 'public');

        $user->update([
            'foto' => $path
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Foto profil berhasil diperbarui',
            'foto_url' => asset('storage/' . $path)
        ], 200);
    }
}