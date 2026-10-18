<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateSuperAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_super_admin_with_default_options(): void
    {
        $this->artisan('make:super-admin')
            ->assertSuccessful()
            ->expectsOutputToContain('Akun Super Admin berhasil dibuat/diperbarui!');

        $this->assertDatabaseHas('users', [
            'username' => 'superadmin',
            'email' => 'superadmin@banksampah.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user = User::where('username', 'superadmin')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_it_creates_super_admin_with_custom_options(): void
    {
        $this->artisan('make:super-admin', [
            '--name' => 'Custom Admin',
            '--username' => 'custom_super',
            '--email' => 'custom@banksampah.test',
            '--password' => 'secret789',
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name' => 'Custom Admin',
            'username' => 'custom_super',
            'email' => 'custom@banksampah.test',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user = User::where('username', 'custom_super')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret789', $user->password));
    }

    public function test_super_admin_can_login_via_web_and_access_admin_dashboard(): void
    {
        $this->artisan('make:super-admin')->assertSuccessful();

        $response = $this->post(route('login'), [
            'email' => 'superadmin@banksampah.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $dashboardResponse = $this->get(route('dashboard'));
        $dashboardResponse->assertOk()
            ->assertSeeText('Kelola User')
            ->assertSeeText('Kelola Nasabah');
    }

    public function test_super_admin_can_login_via_api(): void
    {
        $this->artisan('make:super-admin')->assertSuccessful();

        $response = $this->postJson(route('auth.login'), [
            'login' => 'superadmin',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'admin');
    }
}
