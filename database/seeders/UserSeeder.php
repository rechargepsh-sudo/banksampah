<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin & Super Admin
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@banksampah.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Bank Sampah',
                'email' => 'admin@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Akun Kader dengan Lingkup Wilayah Tangerang Selatan (Kecamatan -> Kelurahan -> RW)
        // Kader 1: Pamulang, Pamulang Barat, RW 05
        $kaderSiti = User::updateOrCreate(
            ['username' => 'kader_siti'],
            [
                'name' => 'Siti Aminah',
                'email' => 'kader.siti@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'is_active' => true,
            ]
        );

        // Kader 2: Pamulang, Pamulang Timur, RW 06
        $kaderBudi = User::updateOrCreate(
            ['username' => 'kader_budi'],
            [
                'name' => 'Budi Santoso',
                'email' => 'kader.budi@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'is_active' => true,
            ]
        );

        // Kader 3: Ciputat, Ciputat, RW 01
        $kaderFauzi = User::updateOrCreate(
            ['username' => 'kader_fauzi'],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'kader.fauzi@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'kecamatan' => 'Ciputat',
                'kelurahan' => 'Ciputat',
                'nomor_rw' => '01',
                'is_active' => true,
            ]
        );

        // Kader 4: Ciputat, Sawah Lama, RW 04
        $kaderDewi = User::updateOrCreate(
            ['username' => 'kader_dewi'],
            [
                'name' => 'Dewi Sartika',
                'email' => 'kader.dewi@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'kecamatan' => 'Ciputat',
                'kelurahan' => 'Sawah Lama',
                'nomor_rw' => '04',
                'is_active' => true,
            ]
        );

        // 3. Akun & Profil Nasabah Fiktif di Tangerang Selatan
        // Dikelompokkan dan disesuaikan berdasarkan wilayah Kecamatan -> Kelurahan -> RW Kader
        $nasabahData = [
            // Wilayah Kader Siti: Kec. Pamulang, Kel. Pamulang Barat, RW 05
            [
                'username' => 'nasabah_dewi',
                'name' => 'Dewi Lestari',
                'email' => 'dewi@banksampah.test',
                'nik' => '3201015502900001',
                'no_hp' => '081234567801',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Surya Kencana No. 12 RT 02 RW 05',
                'tanggal_bergabung' => '2025-01-15',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_agus',
                'name' => 'Agus Pratama',
                'email' => 'agus@banksampah.test',
                'nik' => '3201011208880002',
                'no_hp' => '081234567802',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'nomor_rt' => '03',
                'alamat' => 'Jl. Pajajaran No. 05 RT 03 RW 05',
                'tanggal_bergabung' => '2025-02-10',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_bambang',
                'name' => 'Bambang Pamungkas',
                'email' => 'bambang@banksampah.test',
                'nik' => '3201011005850004',
                'no_hp' => '081234567804',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Dr. Setiabudi No. 14 RT 01 RW 05',
                'tanggal_bergabung' => '2025-04-12',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_siti',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nur@banksampah.test',
                'nik' => '3201014506920005',
                'no_hp' => '081234567805',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'nomor_rt' => '04',
                'alamat' => 'Jl. Witana Harja No. 22 RT 04 RW 05',
                'tanggal_bergabung' => '2025-05-20',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_hendra',
                'name' => 'Hendra Gunawan',
                'email' => 'hendra@banksampah.test',
                'nik' => '3201011509870008',
                'no_hp' => '081234567808',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Barat',
                'nomor_rw' => '05',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Surya Kencana No. 30 RT 02 RW 05',
                'tanggal_bergabung' => '2025-08-30',
                'created_by' => $kaderSiti->id,
            ],

            // Wilayah Kader Budi: Kec. Pamulang, Kel. Pamulang Timur, RW 06
            [
                'username' => 'nasabah_rina',
                'name' => 'Rina Wulandari',
                'email' => 'rina@banksampah.test',
                'nik' => '3201016711950003',
                'no_hp' => '081234567803',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Pinang No. 08 RT 01 RW 06',
                'tanggal_bergabung' => '2025-03-05',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_eko',
                'name' => 'Eko Prasetyo',
                'email' => 'eko@banksampah.test',
                'nik' => '3201012304890006',
                'no_hp' => '081234567806',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Kemuning No. 03 RT 02 RW 06',
                'tanggal_bergabung' => '2025-06-18',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_maya',
                'name' => 'Maya Indah',
                'email' => 'maya@banksampah.test',
                'nik' => '3201015607940007',
                'no_hp' => '081234567807',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'nomor_rt' => '03',
                'alamat' => 'Jl. Beringin No. 19 RT 03 RW 06',
                'tanggal_bergabung' => '2025-07-22',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_fitri',
                'name' => 'Fitri Handayani',
                'email' => 'fitri@banksampah.test',
                'nik' => '3201016212960009',
                'no_hp' => '081234567809',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Pinang No. 15 RT 01 RW 06',
                'tanggal_bergabung' => '2025-09-14',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_joko',
                'name' => 'Joko Widodo',
                'email' => 'joko.w@banksampah.test',
                'nik' => '3201012106800010',
                'no_hp' => '081234567810',
                'kecamatan' => 'Pamulang',
                'kelurahan' => 'Pamulang Timur',
                'nomor_rw' => '06',
                'nomor_rt' => '04',
                'alamat' => 'Jl. Cendana No. 07 RT 04 RW 06',
                'tanggal_bergabung' => '2025-10-01',
                'created_by' => $kaderBudi->id,
            ],

            // Wilayah Kader Fauzi: Kec. Ciputat, Kel. Ciputat, RW 01
            [
                'username' => 'nasabah_fauzi_warga',
                'name' => 'Rahmat Hidayat',
                'email' => 'rahmat.h@banksampah.test',
                'nik' => '3201011804910011',
                'no_hp' => '081234567811',
                'kecamatan' => 'Ciputat',
                'kelurahan' => 'Ciputat',
                'nomor_rw' => '01',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Ki Hajar Dewantara No. 10 RT 01 RW 01',
                'tanggal_bergabung' => '2025-04-01',
                'created_by' => $kaderFauzi->id,
            ],

            // Wilayah Kader Dewi: Kec. Ciputat, Kel. Sawah Lama, RW 04
            [
                'username' => 'nasabah_dewi_warga',
                'name' => 'Anisa Rahmawati',
                'email' => 'anisa.r@banksampah.test',
                'nik' => '3201015903930012',
                'no_hp' => '081234567812',
                'kecamatan' => 'Ciputat',
                'kelurahan' => 'Sawah Lama',
                'nomor_rw' => '04',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Cendrawasih No. 08 RT 02 RW 04',
                'tanggal_bergabung' => '2025-05-15',
                'created_by' => $kaderDewi->id,
            ],
        ];

        foreach ($nasabahData as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'nasabah',
                    'kecamatan' => $data['kecamatan'],
                    'kelurahan' => $data['kelurahan'],
                    'nomor_rw' => $data['nomor_rw'],
                    'is_active' => true,
                ]
            );

            Nasabah::updateOrCreate(
                ['nik' => $data['nik']],
                [
                    'user_id' => $user->id,
                    'nama' => $data['name'],
                    'no_hp' => $data['no_hp'],
                    'kecamatan' => $data['kecamatan'],
                    'kelurahan' => $data['kelurahan'],
                    'nomor_rw' => $data['nomor_rw'],
                    'nomor_rt' => $data['nomor_rt'],
                    'alamat' => $data['alamat'],
                    'tanggal_bergabung' => $data['tanggal_bergabung'],
                    'created_by' => $data['created_by'],
                ]
            );
        }
    }
}
