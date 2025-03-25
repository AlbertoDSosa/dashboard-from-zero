<?php

namespace Tests\Feature\Livewire\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    #[Group('users'), Test]
    public function create_screen_can_be_rendered(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->actingAs($technician);
        $response = $this->get('/users/create');

        $response
            ->assertOk()
            ->assertSeeVolt('users.create');
    }

    #[Group('users'), Test]
    public function new_users_can_create(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->actingAs($technician);

        $component = Volt::test('users.create')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin');

        $component->call('create');

        $component->assertRedirect(route('users', absolute: false));

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user->mainRole);

        $this->assertSame($user->mainRole->name, 'admin');
    }
}
