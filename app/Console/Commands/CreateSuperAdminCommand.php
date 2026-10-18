<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin 
                            {--name=Super Administrator : Nama lengkap Super Admin}
                            {--username=superadmin : Username untuk login}
                            {--email=superadmin@banksampah.test : Email untuk login}
                            {--password=password123 : Password akun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat atau memperbarui akun Super Admin untuk aplikasi Bank Sampah';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = (string) $this->option('name');
        $username = (string) $this->option('username');
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        $user = User::updateOrCreate(
            ['username' => $username],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->newLine();
        $this->info(' Akun Super Admin berhasil dibuat/diperbarui! ');
        $this->newLine();

        $this->table(
            ['Field', 'Nilai'],
            [
                ['Nama', $user->name],
                ['Username', $user->username],
                ['Email', $user->email],
                ['Password', $password],
                ['Role', $user->role],
                ['Status', $user->is_active ? 'Aktif' : 'Non-Aktif'],
            ]
        );

        $this->comment('Gunakan username atau email dan password di atas untuk login melalui Web (/login) atau REST API (/api/auth/login).');
        $this->newLine();

        return Command::SUCCESS;
    }
}
