<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('peraturan_kos', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('isi_poin'); // Bisa disimpan berupa teks dengan pemisah baris atau JSON
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peraturan_kos');
    }
};
