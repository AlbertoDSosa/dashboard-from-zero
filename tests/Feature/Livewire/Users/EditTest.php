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
    public function users_can_edit(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = User::factory()->create();

        $technician = User::factory()->create([
            'email' => 'technician@test.dev'
        ]);

        $admin->assignRole('admin');

        $technician->assignRole('technician');

        $this->actingAs($technician);

        Volt::test('users.edit', ['user' => $technician])
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'passwork')
            ->set('password_confirmation', 'passwork')
            ->set('active', false)
            ->set('role', 'admin')
            ->call('update')
            ->assertRedirect(route('users', absolute: false));

        $technician->refresh();

        $this->assertNull($technician->email_verified_at);

        $this->assertTrue($technician->hasRole('admin'));

        $this->assertSame($technician->name,'Test User' );

    }
}
