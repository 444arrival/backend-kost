<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_pesanan_kantins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_kantin_id')->constrained('pesanan_kantins')->onDelete('cascade');
            $table->integer('menu_id');
            $table->string('nama_menu');
            $table->integer('quantity');
            $table->string('harga');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan_kantins');
    }
};