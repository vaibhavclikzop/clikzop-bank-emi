<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'super_admin',
            'dsa_admin',
            'coordinator',
            'partner',
            'staff',
        ];

        foreach ($permissions as $perm) {
            DB::table('roles')->updateOrInsert([
                'name' => $perm,
            ]);
        }
    }
}
