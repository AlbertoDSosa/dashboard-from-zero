<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

use Tests\TestCase;

class GeneralTest extends TestCase
{
    use RefreshDatabase;

    #[Group('general'), Test]
    public function only_admin_users_can_diplay_log_viewer_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->get('/log-viewer')
            ->assertRedirect('login');

        $this->get('/log-viewer/api/files')
            ->assertRedirect('login');

        $this->actingAs($technician);
        $this->get('/profile');
        $this->get('/log-viewer')
            ->assertRedirect('profile');
        $this->get('/profile');
        $this->get('/log-viewer/api/files')
            ->assertRedirect('profile');

        $this->actingAs($admin);
        $this->get('/log-viewer')
                ->assertSuccessful();

    }
}
