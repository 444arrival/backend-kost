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
    Schema::create('tagihans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('kamar');
        $table->string('periode');
        $table->unsignedBigInteger('nominal');
        $table->date('jatuh_tempo');
        $table->string('status')->default('belum_bayar'); // belum_bayar, menunggu, lunas, gagal
        $table->string('order_id')->nullable()->unique();
        $table->timestamp('dibayar_pada')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('tagihans');
}
};
