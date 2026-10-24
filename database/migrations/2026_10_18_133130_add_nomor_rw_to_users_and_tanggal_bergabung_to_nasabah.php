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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_rw', 5)->nullable()->after('role');
        });

        Schema::table('nasabah', function (Blueprint $table) {
            $table->date('tanggal_bergabung')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropColumn('tanggal_bergabung');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nomor_rw');
        });
    }
};
