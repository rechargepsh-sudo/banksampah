<?php

namespace Database\Seeders;

use App\Models\KategoriSampah;
use Illuminate\Database\Seeder;

class KategoriSampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            [
                'nama_kategori' => 'Kardus / Karton Box',
                'harga_per_kg' => 2500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Botol Plastik Bersih (PET)',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Gelas Plastik / PP',
                'harga_per_kg' => 2000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Logam Besi / Rongsok',
                'harga_per_kg' => 4500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Kaleng Aluminium',
                'harga_per_kg' => 12000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Kertas Putih / HVS',
                'harga_per_kg' => 2200.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Kertas Koran / Buram',
                'harga_per_kg' => 1800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'nama_kategori' => 'Minyak Jelantah',
                'harga_per_kg' => 6000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
        ];

        foreach ($kategori as $item) {
            KategoriSampah::firstOrCreate(
                ['nama_kategori' => $item['nama_kategori']],
                $item
            );
        }
    }
}
