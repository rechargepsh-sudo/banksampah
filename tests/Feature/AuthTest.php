<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_login_with_username(): void
    {
        $kader = User::factory()->kader()->create([
            'username' => 'kader_test',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login' => 'kader_test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'kader_test')
            ->assertJsonPath('data.user.role', 'kader')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'username', 'email', 'role'],
                ],
            ]);
    }

    public function test_nasabah_can_login_with_nik(): void
    {
        $user = User::factory()->nasabah()->create([
            'username' => 'nsb_dewi',
            'password' => Hash::make('nasabah123'),
        ]);

        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'nik' => '3201015502900001',
            'saldo_aktif' => 50000.00,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login' => '3201015502900001',
            'password' => 'nasabah123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'nasabah')
            ->assertJsonPath('data.user.nasabah.nik', '3201015502900001')
            ->assertJsonPath('data.user.nasabah.saldo_aktif', 50000);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'username' => 'user_salah',
            'password' => Hash::make('correct_pass'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login' => 'user_salah',
            'password' => 'wrong_pass',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'username' => 'inactive_user',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'login' => 'inactive_user',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_authenticated_user_can_get_me_and_logout(): void
    {
        $user = User::factory()->kader()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        // Cek me
        $responseMe = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $responseMe->assertStatus(200)
            ->assertJsonPath('data.id', $user->id);

        // Cek logout
        $responseLogout = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $responseLogout->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
