<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User management permissions
            [
                'name' => 'List Users',
                'slug' => 'users-list',
                'description' => 'Ability to view list of users',
            ],
            [
                'name' => 'Create User',
                'slug' => 'users-create',
                'description' => 'Ability to create new users',
            ],
            [
                'name' => 'Read User',
                'slug' => 'users-read',
                'description' => 'Ability to view user details',
            ],
            [
                'name' => 'Update User',
                'slug' => 'users-update',
                'description' => 'Ability to update user information',
            ],
            [
                'name' => 'Delete User',
                'slug' => 'users-delete',
                'description' => 'Ability to delete users',
            ],
            [
                'name' => 'Assign Roles to User',
                'slug' => 'users-assign-roles',
                'description' => 'Ability to assign roles to users',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
