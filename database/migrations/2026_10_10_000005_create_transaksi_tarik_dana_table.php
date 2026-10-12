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
        Schema::create('transaksi_tarik_dana', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penarikan', 30)->unique();
            $table->foreignId('nasabah_id')->constrained('nasabah')->cascadeOnDelete();
            $table->decimal('jumlah_penarikan', 12, 2);
            $table->enum('metode_penarikan', ['tunai', 'transfer'])->default('tunai');
            $table->enum('status', ['pending', 'selesai', 'ditolak'])->default('pending');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_tarik');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tarik_dana');
    }
};
