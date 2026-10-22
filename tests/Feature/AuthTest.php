<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Admin AksesKita',
            'email' => 'admin@akseskita.test',
            'password' => 'rahasia-admin',
        ]);
    }

    public function test_guest_diarahkan_ke_halaman_masuk_pada_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/masuk');
        $this->get('/verifikasi')->assertRedirect('/masuk');
    }

    public function test_kontribusi_tetap_terbuka_untuk_guest(): void
    {
        $this->get('/kontribusi')->assertOk()->assertSee('id="app"', false);
    }

    public function test_login_gagal_dengan_sandi_salah(): void
    {
        $this->admin();

        $this->postJson('/api/login', ['email' => 'admin@akseskita.test', 'password' => 'salah'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_admin_dapat_masuk_dan_membaca_profilnya(): void
    {
        $this->admin();

        $this->postJson('/api/login', ['email' => 'admin@akseskita.test', 'password' => 'rahasia-admin'])
            ->assertOk()
            ->assertJsonPath('user.email', 'admin@akseskita.test');

        $this->assertAuthenticated();

        $this->getJson('/api/me')->assertOk()->assertJsonPath('user.name', 'Admin AksesKita');
        $this->get('/admin')->assertOk()->assertSee('id="app"', false);
        $this->get('/verifikasi')->assertOk();
    }

    public function test_guest_melihat_profil_kosong(): void
    {
        $this->getJson('/api/me')->assertOk()->assertJsonPath('user', null);
    }

    public function test_logout_menghapus_sesi(): void
    {
        $this->actingAs($this->admin())->postJson('/api/logout')->assertOk();

        $this->assertGuest();
        $this->getJson('/api/me')->assertOk()->assertJsonPath('user', null);
    }

    public function test_daftar_akun_hanya_untuk_admin(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();

        $this->actingAs($this->admin())
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonFragment(['email' => 'admin@akseskita.test']);
    }
}
