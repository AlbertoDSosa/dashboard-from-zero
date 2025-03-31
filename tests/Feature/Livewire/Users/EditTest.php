<?php

namespace Tests\Feature\Livewire\Users;

use Livewire\Volt\Volt;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

class EditTest extends TestCase
{
    use RefreshDatabase;

    #[Group('users'), Test]
    public function edit_screen_can_be_rendered(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->actingAs($technician);

        $response = $this->get("/users/edit/{$technician->uuid}");

        $response
            ->assertok()
            ->assertSeeVolt('users.edit');
    }

    #[Group('users'), Test]
    public function only_system_users_can_display_user_edit_page(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($technician);

        $this->get('/profile');

        $this->get("/users/edit/{$user->uuid}")
            ->assertOk()
            ->assertSeeVolt('users.edit');

        $this->actingAs($admin);

        $this->get('/profile');

        $this->get("/users/edit/{$user->uuid}")
            ->assertOk()
            ->assertSeeVolt('users.edit');

        $this->actingAs($customer);

        $this->get('/profile');

        $this->get("/users/edit/{$user->uuid}")
            ->assertRedirectToRoute('profile');
    }

    #[Group('users'), Test]
    public function only_admin_users_can_list_system_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($technician);

        $this->get("/users/edit/{$user->uuid}")
        ->assertDontSeeText('Administrator')
        ->assertDontSeeText('Technician');

        $this->actingAs($admin);

        $this->get("/users/edit/{$user->uuid}")
            ->assertSeeText('Administrator')
            ->assertSeeText('Technician');
    }

    #[Group('users'), Test]
    public function only_admin_users_can_assign_system_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $guest = User::factory()->create();
        $guest->assignRole('guest');

        Volt::actingAs($admin)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User 1')
            ->set('email', 'test1@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($admin)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User 2')
            ->set('email', 'test2@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'technician')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($technician)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User 3')
            ->set('email', 'test3@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'admin')
            ->call('update')
            ->assertStatus(400);

        Volt::actingAs($technician)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User 4')
            ->set('email', 'test4@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('active', true)
            ->set('role', 'technician')
            ->call('update')
            ->assertStatus(400);

    }

    #[Group('users'), Test]
    public function only_system_users_can_edit_users(): void
    {
        // $this->markTestSkipped();
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $guest = User::factory()->create();
        $guest->assignRole('guest');

        Volt::actingAs($technician)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'passwork')
            ->set('password_confirmation', 'passwork')
            ->set('active', false)
            ->set('role', 'guest')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($admin)->test('users.edit', ['user' => $guest])
            ->set('name', 'Test User 1')
            ->set('email', 'test1@example.com')
            ->set('password', 'passwork')
            ->set('password_confirmation', 'passwork')
            ->set('active', false)
            ->set('role', 'admin')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        Volt::actingAs($customer)->test('users.edit', ['user' => $guest])
            ->call('update')
            ->assertStatus(400);

    }

    #[Group('users'), Test]
    public function users_can_edit(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $admin->assignRole('admin');
        $technician = User::factory()->create([
            'email' => 'technician@test.dev'
        ]);

        $technician->assignRole('technician');

        $this->actingAs($technician);

        Volt::test('users.edit', ['user' => $technician])
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'passwork')
            ->set('password_confirmation', 'passwork')
            ->set('active', false)
            ->set('role', 'guest')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        $technician->refresh();

        $this->assertNull($technician->email_verified_at);

        $this->assertTrue($technician->hasRole('guest'));

        $this->assertSame($technician->name,'Test User' );

    }
}
