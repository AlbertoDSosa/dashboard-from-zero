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

        $this->call([
            RolesAndPermissionsSeeder::class
        ]);

        User::factory(37)->create()->each(function ($user) {
            $user->assignRole(collect(['admin', 'technician', 'customer'])->random());
            $user->update([
                'active' => collect([true, false])->random()
            ]);
        });

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
            'active' => false
        ]);

        $customer->assignRole('customer');
    }
}
