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
    public function test_only_system_users_can_display_user_create_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($technician);

        $this->get('/profile');

        $this->get('/users/create')
            ->assertOk()
            ->assertSeeVolt('users.create');

        $this->actingAs($admin);

        $this->get('/profile');

        $this->get('/users/create')
            ->assertOk()
            ->assertSeeVolt('users.create');

        $this->actingAs($customer);

        $this->get('/profile');

        $this->get('/users/create')
            ->assertRedirectToRoute('profile');
    }

    #[Group('users'), Test]
    public function only_system_users_can_create_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Volt::actingAs($technician)->test('users.create')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'customer')
            ->call('create')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($admin)->test('users.create')
            ->set('name', 'Test User 1')
            ->set('email', 'test1@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin')
            ->call('create')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($customer)->test('users.create')
            ->set('name', 'Test User 2')
            ->set('email', 'test2@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'customer')
            ->call('create')
            ->assertStatus(400);
    }

    #[Group('users'), Test]
    public function only_admin_users_can_list_system_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');


        $this->actingAs($technician);

        $this->get('/users/create')
        ->assertDontSeeText('Administrator')
        ->assertDontSeeText('Technician');

        $this->actingAs($admin);

        $this->get('/users/create')
            ->assertSeeText('Administrator')
            ->assertSeeText('Technician');
    }

    #[Group('users'), Test]
    public function only_admin_users_can_assign_system_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');
        $technician->assignRole('technician');


        Volt::actingAs($admin)->test('users.create')
            ->set('name', 'Test User 1')
            ->set('email', 'test1@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin')
            ->call('create')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($admin)->test('users.create')
            ->set('name', 'Test User 2')
            ->set('email', 'test2@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'technician')
            ->call('create')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($technician)->test('users.create')
            ->set('name', 'Test User 3')
            ->set('email', 'test3@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin')
            ->call('create')
            ->assertStatus(400);

        Volt::actingAs($technician)->test('users.create')
            ->set('name', 'Test User 4')
            ->set('email', 'test4@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'technician')
            ->call('create')
            ->assertStatus(400);

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
            ->set('role', 'guest');

        $component->call('create');

        $component->assertRedirect(route('users', absolute: false));

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user->mainRole);

        $this->assertSame($user->mainRole->name, 'guest');
    }
}
