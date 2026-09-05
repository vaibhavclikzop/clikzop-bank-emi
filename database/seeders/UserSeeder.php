<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@bankemi.com',
            'password' => Hash::make('123456'),
            'role' => 'super_admin',
            'parent_id' => null,
            'tenant_id' => null,
        ]);
    }
}
