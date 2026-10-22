<?php

namespace Tests\Feature;

use Tests\TestCase;

class AksesKitaTest extends TestCase
{
    public function test_beranda_menampilkan_shell_aplikasi_vue(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="app"', false);
        $response->assertSee('AksesKita', false);
    }

    public function test_rute_spa_mengembalikan_shell_yang_sama(): void
    {
        $this->get('/fasilitas/AKS-0001')->assertOk()->assertSee('id="app"', false);
        $this->get('/kontribusi')->assertOk()->assertSee('id="app"', false);
        $this->get('/tentang')->assertOk()->assertSee('id="app"', false);
    }

    public function test_health_check_tidak_tertutup_rute_spa(): void
    {
        $this->get('/up')->assertOk();
    }
}
