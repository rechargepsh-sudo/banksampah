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
        Schema::create('detail_transaksi_setor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_setor_id')->constrained('transaksi_setor')->cascadeOnDelete();
            $table->foreignId('kategori_sampah_id')->constrained('kategori_sampah')->restrictOnDelete();
            $table->decimal('berat', 8, 2);
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_setor');
    }
};
