<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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

        $arrayOfPermissionNames = [
            ['name' => 'list users'],
            ['name' => 'show user ids'],
            ['name' => 'show system users'],
            ['name' => 'create users'],
            ['name' => 'create system users'],
            ['name' => 'edit users'],
            ['name' => 'edit system users'],
            ['name' => 'delete users'],
            ['name' => 'delete system users'],
            ['name' => 'show roles'],
            ['name' => 'assign roles'],
            ['name' => 'assign system roles']
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission['name'],
                'guard_name' => 'web',
                'created_at' => now()
            ];
        });

        Permission::insert($permissions->toArray());

        $admin = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
            'display_name' => 'Administrator',
            'is_from_system' => true
        ]);

        $admin->givePermissionTo([
            'list users',
            'show system users',
            'show user ids',
            'create users',
            'create system users',
            'edit users',
            'edit system users',
            'delete users',
            'delete system users',
            'show roles',
            'assign roles',
            'assign system roles'
        ]);

        $technician = Role::create([
            'name' => 'technician',
            'guard_name' => 'web',
            'display_name' => 'Technician',
            'is_from_system' => true
        ]);

        $technician->givePermissionTo([
            'list users',
            'create users',
            'assign roles',
            'edit users'
        ]);

        $customer = Role::create([
            'name' => 'customer',
            'guard_name' => 'web',
            'display_name' => 'Customer'
        ]);

        // $customer->givePermissionTo([]);

        $guest = Role::create([
            'name' => 'guest',
            'display_name' => 'Guest'
        ]);

        // $guest->givePermissionTo([]);
    }
}
