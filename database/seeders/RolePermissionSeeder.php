<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();
        $permissions = Permission::pluck('id');

        foreach ($roles as $role) {

            if ($role->name === 'super_admin') {
                $role->permissions()->sync($permissions); // all
            } else {
                $role->permissions()->sync([]); // empty by default
            }
        }
    }
}
