<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PermissionSeeder::class,
            LoanStatusSeeder::class,
            StateDistrict::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            CommonDocumentSeeder::class,
            BankSeeder::class,
            VanillaFieldSeeder::class,
        ]);
        User::updateOrCreate(
            [
                'email' => 'admin@bankemi.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'role' => 'super_admin',
                'parent_id' => null,
                'tenant_id' => null,
            ]
        );
    }
}
