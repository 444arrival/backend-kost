<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanan_kantins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penghuni');
            $table->string('nomor_kamar');
            $table->decimal('total_harga', 10, 2);
            $table->text('catatan')->nullable();
            $table->string('status')->default('Diproses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_kantins');
    }
};