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
        // 1. Akun Admin (1 user)
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

        // 2. Akun Kader (2 user)
        $kaderSiti = User::firstOrCreate(
            ['username' => 'kader_siti'],
            [
                'name' => 'Siti Aminah',
                'email' => 'kader.siti@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'is_active' => true,
            ]
        );

        $kaderBudi = User::firstOrCreate(
            ['username' => 'kader_budi'],
            [
                'name' => 'Budi Santoso',
                'email' => 'kader.budi@banksampah.test',
                'password' => Hash::make('password'),
                'role' => 'kader',
                'is_active' => true,
            ]
        );

        // 3. Akun & Profil Nasabah Fiktif (10 user + 10 nasabah)
        $nasabahData = [
            [
                'username' => 'nasabah_dewi',
                'name' => 'Dewi Lestari',
                'email' => 'dewi@banksampah.test',
                'nik' => '3201015502900001',
                'no_hp' => '081234567801',
                'nomor_rw' => '05',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Melati No. 12 RT 02 RW 05',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_agus',
                'name' => 'Agus Pratama',
                'email' => 'agus@banksampah.test',
                'nik' => '3201011208880002',
                'no_hp' => '081234567802',
                'nomor_rw' => '05',
                'nomor_rt' => '03',
                'alamat' => 'Jl. Mawar No. 05 RT 03 RW 05',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_rina',
                'name' => 'Rina Wulandari',
                'email' => 'rina@banksampah.test',
                'nik' => '3201016711950003',
                'no_hp' => '081234567803',
                'nomor_rw' => '06',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Kenanga No. 08 RT 01 RW 06',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_bambang',
                'name' => 'Bambang Pamungkas',
                'email' => 'bambang@banksampah.test',
                'nik' => '3201011005850004',
                'no_hp' => '081234567804',
                'nomor_rw' => '05',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Cempaka No. 14 RT 01 RW 05',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_siti',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nur@banksampah.test',
                'nik' => '3201014506920005',
                'no_hp' => '081234567805',
                'nomor_rw' => '05',
                'nomor_rt' => '04',
                'alamat' => 'Jl. Flamboyan No. 22 RT 04 RW 05',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_eko',
                'name' => 'Eko Prasetyo',
                'email' => 'eko@banksampah.test',
                'nik' => '3201012304890006',
                'no_hp' => '081234567806',
                'nomor_rw' => '06',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Dahlia No. 03 RT 02 RW 06',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_maya',
                'name' => 'Maya Indah',
                'email' => 'maya@banksampah.test',
                'nik' => '3201015607940007',
                'no_hp' => '081234567807',
                'nomor_rw' => '06',
                'nomor_rt' => '03',
                'alamat' => 'Jl. Kamboja No. 19 RT 03 RW 06',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_hendra',
                'name' => 'Hendra Gunawan',
                'email' => 'hendra@banksampah.test',
                'nik' => '3201011509870008',
                'no_hp' => '081234567808',
                'nomor_rw' => '05',
                'nomor_rt' => '02',
                'alamat' => 'Jl. Melati No. 30 RT 02 RW 05',
                'created_by' => $kaderSiti->id,
            ],
            [
                'username' => 'nasabah_fitri',
                'name' => 'Fitri Handayani',
                'email' => 'fitri@banksampah.test',
                'nik' => '3201016212960009',
                'no_hp' => '081234567809',
                'nomor_rw' => '06',
                'nomor_rt' => '01',
                'alamat' => 'Jl. Kenanga No. 15 RT 01 RW 06',
                'created_by' => $kaderBudi->id,
            ],
            [
                'username' => 'nasabah_joko',
                'name' => 'Joko Widodo',
                'email' => 'joko.w@banksampah.test',
                'nik' => '3201012106800010',
                'no_hp' => '081234567810',
                'nomor_rw' => '06',
                'nomor_rt' => '04',
                'alamat' => 'Jl. Tanjung No. 07 RT 04 RW 06',
                'created_by' => $kaderBudi->id,
            ],
        ];

        foreach ($nasabahData as $data) {
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'nasabah',
                    'is_active' => true,
                ]
            );

            Nasabah::firstOrCreate(
                ['nik' => $data['nik']],
                [
                    'user_id' => $user->id,
                    'nama' => $data['name'],
                    'no_hp' => $data['no_hp'],
                    'nomor_rw' => $data['nomor_rw'],
                    'nomor_rt' => $data['nomor_rt'],
                    'alamat' => $data['alamat'],
                    'saldo_aktif' => 0.00,
                    'created_by' => $data['created_by'],
                ]
            );
        }
    }
}
