<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VanillaFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['field_name' => 'net_profit',               'display_name' => 'Net Profit',                 'display_order' => 1],
            ['field_name' => 'depreciation',             'display_name' => 'Depreciation',               'display_order' => 2],
            ['field_name' => 'interest_on_secured_loan', 'display_name' => 'Interest on Secured Loan',   'display_order' => 3],
            ['field_name' => 'director_salary',          'display_name' => 'Director Salary',            'display_order' => 4],
            ['field_name' => 'interest_on_capital',      'display_name' => 'Interest on Capital',        'display_order' => 5],
            ['field_name' => 'remuneration',             'display_name' => 'Remuneration',               'display_order' => 6],
            ['field_name' => 'salary',                   'display_name' => 'Salary',                     'display_order' => 7],
            ['field_name' => 'other_income',             'display_name' => 'Other Income',               'display_order' => 8],
            ['field_name' => 'commission_income',        'display_name' => 'Commission Income',          'display_order' => 9],
            ['field_name' => 'rental_income',            'display_name' => 'Rental Income',              'display_order' => 10],
            ['field_name' => 'income_tax',               'display_name' => 'Less: Income Tax',           'display_order' => 11],
        ];
        foreach ($data as $status) {
            DB::table('vanilla_fields')->updateOrInsert(
                ['field_name' => $status['field_name']],
                [
                    'display_name'  => $status['display_name'],
                    'display_order' => $status['display_order'],
                ]
            );
        }
    }
}
