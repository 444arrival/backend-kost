<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\Api\MenuKantinController;
use App\Http\Controllers\PesananKantinController;

Route::get('/user', function (Request $request) {
    $user = $request->user();
    return response()->json([
        'status' => true,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'nomor_kamar' => $user->nomor_kamar,
            'role' => $user->role,
            'foto' => $user->foto ? asset('storage/' . $user->foto) : null
        ]
    ]);
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/tambah-penghuni', [AdminController::class, 'tambahPenghuni']);


Route::get('/menu-kantin', [MenuKantinController::class, 'index']);
Route::post('/menu-kantin', [MenuKantinController::class, 'store']);
Route::post('/menu-kantin/{id}', [MenuKantinController::class, 'update']);
Route::delete('/menu-kantin/{id}', [MenuKantinController::class, 'destroy']);


Route::get('/pesanan-kantin', [PesananKantinController::class, 'index']);
Route::post('/pesanan-kantin', [PesananKantinController::class, 'store']);
Route::post('/pesanan-kantin/checkout', [PesananKantinController::class, 'checkout']);
Route::put('/pesanan-kantin/{id}/status', [PesananKantinController::class, 'updateStatus']);


Route::get('/admin/penghuni', [AdminController::class, 'getPenghuni']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/pengaduans', [PengaduanController::class, 'index']);
    Route::post('/pengaduans', [PengaduanController::class, 'store']);
    
    Route::get('/admin/pengaduan', [PengaduanController::class, 'index']); 
    Route::put('/admin/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus']);

    Route::get('/pengumuman', [PengumumanController::class, 'index']);
    Route::post('/admin/pengumuman', [PengumumanController::class, 'store']);

    Route::post('/user/foto', [AuthController::class, 'updateFoto']);
});