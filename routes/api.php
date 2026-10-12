<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PengaduanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/admin/tambah-penghuni', [AdminController::class, 'tambahPenghuni']);

Route::get('/admin/penghuni', [AdminController::class, 'getPenghuni']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pengaduans', [PengaduanController::class, 'index']);
    Route::post('/pengaduans', [PengaduanController::class, 'store']);
});