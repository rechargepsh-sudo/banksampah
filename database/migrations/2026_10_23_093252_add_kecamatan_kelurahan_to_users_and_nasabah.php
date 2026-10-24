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
            $table->string('kecamatan', 60)->nullable()->after('role');
            $table->string('kelurahan', 60)->nullable()->after('kecamatan');
        });

        Schema::table('nasabah', function (Blueprint $table) {
            $table->string('kecamatan', 60)->nullable()->after('no_hp');
            $table->string('kelurahan', 60)->nullable()->after('kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });
    }
};
