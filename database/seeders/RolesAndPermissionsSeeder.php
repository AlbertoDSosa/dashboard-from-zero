<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $technician = Role::create([
            'name' => 'technician',
            'guard_name' => 'web'
        ]);

        $customer = Role::create([
            'name' => 'customer',
            'guard_name' => 'web'
        ]);
    }
}
