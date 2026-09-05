<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create_customer',
            'view_customer',
            'update_customer',
            'create_loan',
            'view_loan',
            'edit_loan',
            'delete_loan',
            'view_company_profile',
            'update_company_profile',
            'create_user',
            'view_user',
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $perm],
                ['name' => $perm]
            );
        }
        DB::table('user_roles')->updateOrInsert([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
