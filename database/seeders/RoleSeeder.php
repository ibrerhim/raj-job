<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super Administrator with all permissions',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with administrative permissions',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with limited administrative permissions',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user with basic permissions',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $manager = Role::where('slug', 'manager')->first();
        $user = Role::where('slug', 'user')->first();

        // Super Admin gets all permissions
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $superAdmin->permissions()->sync($allPermissions);

        // Admin gets all permissions
        $admin->permissions()->sync($allPermissions);

        // Manager gets list, read, and create permissions
        $managerPermissions = Permission::whereIn('slug', [
            'users-list',
            'users-read',
            'users-create',
            'users-update',
        ])->pluck('id')->toArray();
        $manager->permissions()->sync($managerPermissions);

        // User gets only list and read permissions
        $userPermissions = Permission::whereIn('slug', [
            'users-list',
            'users-read',
        ])->pluck('id')->toArray();
        $user->permissions()->sync($userPermissions);
    }
}
