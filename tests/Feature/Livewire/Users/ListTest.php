<?php

namespace Tests\Feature\Livewire\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    #[Group('users'), Test]
    public function test_it_can_render(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->actingAs($technician);

        $response = $this->get('/users');

        $response
            ->assertOk()
            ->assertSeeVolt('users.list');

    }

    #[Group('users'), Test]
    public function can_delete_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        Volt::actingAs($admin)
            ->test('users.list')
            ->call('delete', $technician->id)
            ->assertOk();

        $this->assertNull(User::find($technician->id));

    }
}
