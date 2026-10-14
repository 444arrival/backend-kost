<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\Api\MenuKantinController;
use App\Http\Controllers\PesananKantinController;

Route::get('/user', function (Request $request) {
    return $request->user();
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
});