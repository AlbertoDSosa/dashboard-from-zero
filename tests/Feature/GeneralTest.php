<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Tests\TestCase;

class GeneralTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_only_admin_users_can_diplay_log_viewer_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->get('/log-viewer')
            ->assertForbidden();

        $this->actingAs($technician);
        $this->get('/log-viewer')
            ->assertForbidden();

        $this->actingAs($admin);
        $this->get('/log-viewer')
                ->assertSuccessful();

    }
}
