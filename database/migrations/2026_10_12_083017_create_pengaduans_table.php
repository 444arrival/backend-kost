<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            // Menghubungkan pengaduan dengan tabel users (penghuni yang login)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('kategori'); // Contoh: AC, Wifi, Listrik, dll
            $table->string('judul');    // Judul atau ringkasan kendala
            $table->text('deskripsi');  // Penjelasan detail kendala
            $table->string('status')->default('Menunggu'); // Status: Menunggu, Diproses, Selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
