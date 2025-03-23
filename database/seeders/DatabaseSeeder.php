<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(30)->create();

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        $admin = User::factory()->create([
            'name' => 'Administrator User',
            'email' => 'admin@example.com',
        ]);

        $admin->assignRole('admin');

        $technician = User::factory()->create([
            'name' => 'Technician User',
            'email' => 'technician@example.com',
        ]);

        $technician->assignRole('technician');

        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
        ]);

        $customer->assignRole('customer');
    }
}
