<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateDistrict extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('state_district')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sql = file_get_contents(database_path('seeders/state_city.sql'));
        DB::unprepared($sql);
    }
}
