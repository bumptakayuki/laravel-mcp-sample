<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_ats_dashboard_renders(): void
    {
        $response = $this->get('/ats');

        $response->assertOk();
        $response->assertSee('ダッシュボード', false);
    }
}
