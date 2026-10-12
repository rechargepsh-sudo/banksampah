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
        Schema::create('mutasi_saldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah')->cascadeOnDelete();
            $table->enum('jenis_mutasi', ['masuk', 'keluar']);
            $table->decimal('nominal', 12, 2);
            $table->decimal('saldo_sebelum', 12, 2);
            $table->decimal('saldo_sesudah', 12, 2);
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('tipe_referensi', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_saldo');
    }
};
