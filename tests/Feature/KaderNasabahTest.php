<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaderNasabahTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_register_new_nasabah_with_auto_generated_credentials(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $payload = [
            'nik' => '3201011122330005',
            'nama' => 'Bambang Sudirman',
            'no_hp' => '081299887766',
            'nomor_rw' => '07',
            'nomor_rt' => '03',
            'alamat' => 'Jl. Anggrek No. 17',
        ];

        $response = $this->postJson('/api/kader/nasabah', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nasabah.nik', '3201011122330005')
            ->assertJsonPath('data.nasabah.nama', 'Bambang Sudirman')
            ->assertJsonPath('data.nasabah.saldo_aktif', 0)
            ->assertJsonStructure([
                'data' => [
                    'nasabah' => ['id', 'user_id', 'nik', 'nama', 'no_hp', 'nomor_rw', 'saldo_aktif'],
                    'credentials' => ['username', 'initial_password', 'info'],
                ],
            ]);

        $this->assertDatabaseHas('nasabah', [
            'nik' => '3201011122330005',
            'nama' => 'Bambang Sudirman',
            'nomor_rw' => '07',
            'created_by' => $kader->id,
        ]);

        $createdNasabah = Nasabah::where('nik', '3201011122330005')->first();
        $this->assertNotNull($createdNasabah->user);
        $this->assertSame('nasabah', $createdNasabah->user->role);

        // Verifikasi bahwa password awal yang digenerate dapat digunakan untuk login
        $initialPassword = $response->json('data.credentials.initial_password');
        $this->assertTrue(Hash::check($initialPassword, $createdNasabah->user->password));
    }

    public function test_nasabah_cannot_register_other_nasabah(): void
    {
        $nasabahUser = User::factory()->nasabah()->create();
        Sanctum::actingAs($nasabahUser);

        $response = $this->postJson('/api/kader/nasabah', [
            'nik' => '3201011122330006',
            'nama' => 'User Palsu',
            'no_hp' => '081234567899',
            'nomor_rw' => '01',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson('/api/kader/nasabah', [
            'nik' => '3201011122330007',
            'nama' => 'User Guest',
            'no_hp' => '081234567899',
            'nomor_rw' => '01',
        ]);

        $response->assertStatus(401);
    }

    public function test_registration_validation_rules(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        // NIK kurang dari 16 digit
        $responseShortNik = $this->postJson('/api/kader/nasabah', [
            'nik' => '12345',
            'nama' => 'Nama Test',
            'no_hp' => '081234567899',
            'nomor_rw' => '01',
        ]);
        $responseShortNik->assertStatus(422)
            ->assertJsonValidationErrors(['nik']);

        // NIK duplicate
        Nasabah::factory()->create(['nik' => '3201019999990001']);
        $responseDuplicate = $this->postJson('/api/kader/nasabah', [
            'nik' => '3201019999990001',
            'nama' => 'Nama Test 2',
            'no_hp' => '081234567899',
            'nomor_rw' => '01',
        ]);
        $responseDuplicate->assertStatus(422)
            ->assertJsonValidationErrors(['nik']);
    }

    public function test_kader_can_list_and_search_nasabah(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        Nasabah::factory()->create(['nama' => 'Zulfa Halim', 'nomor_rw' => '04']);
        Nasabah::factory()->create(['nama' => 'Andi Wijaya', 'nomor_rw' => '05']);

        $response = $this->getJson('/api/kader/nasabah?search=Zulfa');
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertSame('Zulfa Halim', $data[0]['nama']);
    }
}
