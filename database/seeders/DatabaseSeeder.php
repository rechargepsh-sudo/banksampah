<?php

namespace Database\Seeders;

use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset tabel agar seeder bersifat idempoten dan deterministik
        Schema::disableForeignKeyConstraints();
        MutasiSaldo::truncate();
        TransaksiTarikDana::truncate();
        DetailTransaksiSetor::truncate();
        TransaksiSetor::truncate();
        Nasabah::truncate();
        KategoriSampah::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            KategoriSampahSeeder::class,
            UserSeeder::class,
            TransaksiDummySeeder::class,
        ]);
    }
}
