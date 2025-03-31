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
    public function only_system_users_can_display_user_list_page(): void
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

        $this->get('/users')
        ->assertOk()
        ->assertSeeVolt('users.list');

        $this->actingAs($admin);

        $this->get('/profile');

        $this->get('/users')
        ->assertOk()
        ->assertSeeVolt('users.list');

        $this->actingAs($customer);

        $this->get('/profile');

        $this->get('/users')
            ->assertRedirectToRoute('profile');
    }

    #[Group('users'), Test]
    public function only_admin_users_can_delete_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create();

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $user = User::factory()->create();
        $user->assignRole('customer');

        Volt::actingAs($technician)
            ->test('users.list')
            ->call('delete', $user->uuid)
            ->assertStatus(400);

        Volt::actingAs($customer)
            ->test('users.list')
            ->call('delete', $user->uuid)
            ->assertStatus(400);

        Volt::actingAs($admin)
            ->test('users.list')
            ->call('delete', $user->uuid)
            ->assertOk();
    }

    #[Group('users'), Test]
    public function can_delete_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $technician = User::factory()->create();
        $technician->assignRole('technician');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Volt::actingAs($technician)
            ->test('users.list')
            ->call('delete', $customer->id)
            ->assertStatus(400);

        Volt::actingAs($admin)
            ->test('users.list')
            ->call('delete', $customer->uuid)
            ->assertOk();

        $this->assertNull(User::find($customer->uuid));

    }
}
