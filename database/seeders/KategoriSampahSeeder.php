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
        // 68 Kategori Sampah sesuai Daftar Harga Resmi Bank Sampah (Oktober 2026)
        $kategori = [
            // ========================================================
            // KELOMPOK 1: LOGAM, BESI, TEMBAGA & ELEKTRONIK CAMPUR
            // ========================================================
            [
                'kode' => '1A',
                'nama_kategori' => 'ALUMUNIUM',
                'harga_per_kg' => 12000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1B',
                'nama_kategori' => 'BESI',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1C',
                'nama_kategori' => 'BABET / KRAN BESI',
                'harga_per_kg' => 10000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1D',
                'nama_kategori' => 'KALENG',
                'harga_per_kg' => 1400.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1E',
                'nama_kategori' => 'KABEL',
                'harga_per_kg' => 800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1F',
                'nama_kategori' => 'KUNINGAN',
                'harga_per_kg' => 45000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1G',
                'nama_kategori' => 'KAWAT / SENG',
                'harga_per_kg' => 800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1H',
                'nama_kategori' => 'STAINLESS',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1I',
                'nama_kategori' => 'TEMBAGA KUPAS',
                'harga_per_kg' => 120000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '1J',
                'nama_kategori' => 'KABIN / VCD PLAYER / ELEKTRONIK / SEPEDA',
                'harga_per_kg' => 1700.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 2: ANEKA PLASTIK
            // ========================================================
            [
                'kode' => '2A',
                'nama_kategori' => 'ASOY ( PLASTIK KRESEK / BUBBLE WRAP )',
                'harga_per_kg' => 250.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2B',
                'nama_kategori' => 'MLP ( MULTI LAYER PLASTIK / PLASTIK MINYAK GORENG )',
                'harga_per_kg' => 200.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2C',
                'nama_kategori' => 'MIKA TEBAL',
                'harga_per_kg' => 100.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2D',
                'nama_kategori' => 'BOTOL MINERAL BERSIH ( LEPAS LABEL & LEPAS TUTUP )',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2E',
                'nama_kategori' => 'GALON LE MINERALE SAJA',
                'harga_per_kg' => 2700.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '2F',
                'nama_kategori' => 'BOTOL MINERAL KOTOR ( MASIH ADA LABEL )',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2G',
                'nama_kategori' => 'BOTOL WARNA ( BODONG WARNA )',
                'harga_per_kg' => 500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2H',
                'nama_kategori' => 'EMBER CAMPUR',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2I',
                'nama_kategori' => 'EMBER YANG BERWARNA HITAM',
                'harga_per_kg' => 600.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2J',
                'nama_kategori' => 'GALON MERK AQUA ( SATUAN )',
                'harga_per_kg' => 3000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '2K',
                'nama_kategori' => 'GELAS MINERAL A ( TDK ADA WARNA )',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2L',
                'nama_kategori' => 'GELAS MINERAL B ( ADA WARNA )',
                'harga_per_kg' => 1300.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2M',
                'nama_kategori' => 'GELAS KOTOR ( TDK ADA AIR ) ALE-ALE',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2N',
                'nama_kategori' => 'PE ( PLASTIK, BUBBLE WRAP PUTIH )',
                'harga_per_kg' => 500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2O',
                'nama_kategori' => 'PVC ( PARALON ) / PINTU KMR MANDI',
                'harga_per_kg' => 500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2P',
                'nama_kategori' => 'TUTUP BOTOL MINERAL MURNI',
                'harga_per_kg' => 2000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2Q',
                'nama_kategori' => 'TUTUP GALON ( TDK TERMASUK LE MINERALE )',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2R',
                'nama_kategori' => 'INJEK BERSIH / THIN WALL TANPA LABEL',
                'harga_per_kg' => 1700.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '2S',
                'nama_kategori' => 'INJEK LABEL DAN INJEK SABLON',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 3: KERTAS & KARDUS
            // ========================================================
            [
                'kode' => '3A',
                'nama_kategori' => 'BUKU CAMPUR ( TDK TERMASUK MAJALAH )',
                'harga_per_kg' => 1100.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3B',
                'nama_kategori' => 'BUKU LKS / MAJALAH / KOMIK',
                'harga_per_kg' => 800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3C',
                'nama_kategori' => 'BONCOS DUPLEK',
                'harga_per_kg' => 500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3D',
                'nama_kategori' => 'TETRAPACK',
                'harga_per_kg' => 100.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3E',
                'nama_kategori' => 'KORAN BACA ( BERSIH )',
                'harga_per_kg' => 1300.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3F',
                'nama_kategori' => 'KARDUS',
                'harga_per_kg' => 1300.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3G',
                'nama_kategori' => 'KARDUS BASAH',
                'harga_per_kg' => 400.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3H',
                'nama_kategori' => 'KANTONG SEMEN',
                'harga_per_kg' => 1000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3I',
                'nama_kategori' => 'PUTIHAN / HVS ( TDK TERMASUK BUKU PLJRN )',
                'harga_per_kg' => 1400.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3J',
                'nama_kategori' => 'STEROFOAM TEBAL ( BEKAS ELEKTRONIK )',
                'harga_per_kg' => 200.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '3K',
                'nama_kategori' => 'TRY TELOR',
                'harga_per_kg' => 250.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 4: BELING & KACA
            // ========================================================
            [
                'kode' => '4A',
                'nama_kategori' => 'BOTOL BELING CAMPUR, SIRUP /KG',
                'harga_per_kg' => 200.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '4B',
                'nama_kategori' => 'BELING PECAH',
                'harga_per_kg' => 100.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '4C',
                'nama_kategori' => 'BOTOL KECAP ( SATUAN )',
                'harga_per_kg' => 200.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '4D',
                'nama_kategori' => 'BOTOL BIR ( HANYA BTL MERK BIR ) SATUAN',
                'harga_per_kg' => 400.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 5: AKRILIK & KASET
            // ========================================================
            [
                'kode' => '5A',
                'nama_kategori' => 'AKRILIK / KRISTAL / TOPLES TIPIS',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '5B',
                'nama_kategori' => 'KASET CD ( BERSIH, TDK ADA KOTAKNYA )',
                'harga_per_kg' => 3000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 6: ELEKTRONIK & PERALATAN RUMAH TANGGA
            // ========================================================
            [
                'kode' => '6A',
                'nama_kategori' => 'IMPACT / YAKULT / MAINAN ANAK2 / SPAKBOR MOBIL / MOTOR',
                'harga_per_kg' => 700.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '6B',
                'nama_kategori' => 'AC 1 SET 2 PK KEATAS ( INDOR & OUTDOR & PIPA ) SATUAN',
                'harga_per_kg' => 140000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6C',
                'nama_kategori' => 'AC 1 SET 2 PK KEBAWAH ( INDOR & OUTDOR & PIPA ) SATUAN',
                'harga_per_kg' => 140000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6D',
                'nama_kategori' => 'CPU LENGKAP ( SATUAN )',
                'harga_per_kg' => 40000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6E',
                'nama_kategori' => 'HP / KG',
                'harga_per_kg' => 7000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '6F',
                'nama_kategori' => 'KULKAS ( SATUAN )',
                'harga_per_kg' => 60000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6G',
                'nama_kategori' => 'KOMPUTER / MONITOR / KOMPLIT ( SATUAN )',
                'harga_per_kg' => 4500.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6H',
                'nama_kategori' => 'MESIN CUCI ( SATUAN )',
                'harga_per_kg' => 30000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6I',
                'nama_kategori' => 'MESIN AC / KG',
                'harga_per_kg' => 2500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '6J',
                'nama_kategori' => 'TV FLAT ( SATUAN )',
                'harga_per_kg' => 18000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '6K',
                'nama_kategori' => 'TV TABUNG ( SATUAN )',
                'harga_per_kg' => 13000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],

            // ========================================================
            // KELOMPOK 7: KHUSUS, JELANTAH, OLI & LAIN-LAIN
            // ========================================================
            [
                'kode' => '7A',
                'nama_kategori' => 'ACCU / KG ( TDK ADA AIR )',
                'harga_per_kg' => 8000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7B',
                'nama_kategori' => 'CAMPUR / GABRUK',
                'harga_per_kg' => 800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7C',
                'nama_kategori' => 'KARUNG / KG',
                'harga_per_kg' => 300.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7D',
                'nama_kategori' => 'JELANTAH ( TANPA AMPAS )',
                'harga_per_kg' => 5000.00,
                'satuan' => 'liter',
                'is_active' => true,
            ],
            [
                'kode' => '7E',
                'nama_kategori' => 'KARPET TALANG / KARPET MERAH',
                'harga_per_kg' => 200.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7F',
                'nama_kategori' => 'TUPPERWARE',
                'harga_per_kg' => 1500.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7G',
                'nama_kategori' => 'KIPAS ANGIN BESAR BERDIRI / PCS',
                'harga_per_kg' => 14000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '7H',
                'nama_kategori' => 'MONITOR SLIM ( SATUAN )',
                'harga_per_kg' => 4500.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '7I',
                'nama_kategori' => 'MONITOR TABUNG ( SATUAN )',
                'harga_per_kg' => 16000.00,
                'satuan' => 'pcs',
                'is_active' => true,
            ],
            [
                'kode' => '7J',
                'nama_kategori' => 'OLI / KG',
                'harga_per_kg' => 800.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
            [
                'kode' => '7K',
                'nama_kategori' => 'DIRIGENT NASO ( DIRIGEN BERSIH, TDK BOCOR )',
                'harga_per_kg' => 2000.00,
                'satuan' => 'kg',
                'is_active' => true,
            ],
        ];

        // Pemetaan kompatibilitas nama kategori lama -> kode baru
        $legacyAliases = [
            'Kardus / Karton Box' => '3F',
            'Botol Plastik Bersih (PET)' => '2D',
            'Gelas Plastik / PP' => '2K',
            'Logam Besi / Rongsok' => '1B',
            'Kaleng Aluminium' => '1D',
            'Kertas Putih / HVS' => '3I',
            'Kertas Koran / Buram' => '3E',
            'Minyak Jelantah' => '7D',
        ];

        // 1. Tautkan kategori lama yang sudah ada di database ke kode masing-masing
        foreach ($legacyAliases as $legacyName => $kode) {
            $existing = KategoriSampah::where('nama_kategori', $legacyName)->first();
            if ($existing) {
                $targetItem = collect($kategori)->firstWhere('kode', $kode);
                if ($targetItem) {
                    $existing->update([
                        'kode' => $kode,
                        'nama_kategori' => $targetItem['nama_kategori'],
                        'harga_per_kg' => $targetItem['harga_per_kg'],
                        'satuan' => $targetItem['satuan'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 2. Simpan atau perbarui seluruh 68 kategori
        foreach ($kategori as $item) {
            KategoriSampah::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
