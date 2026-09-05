<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Lead Created', 'step_order' => 1],
            ['name' => 'Intent Verified', 'step_order' => 2],
            ['name' => 'R&D Research', 'step_order' => 3],
            ['name' => 'Coordinator Review', 'step_order' => 4],
            ['name' => 'Partner Assigned', 'step_order' => 5],
            ['name' => 'API Verification', 'step_order' => 6],
            ['name' => 'Eligibility Checked', 'step_order' => 7],
            ['name' => 'Bank Selected', 'step_order' => 8],
            ['name' => 'Application Created', 'step_order' => 9],
            ['name' => 'Operations Processing', 'step_order' => 10],
            ['name' => 'Submitted to Bank', 'step_order' => 11],
            ['name' => 'Bank Processing', 'step_order' => 12],
            ['name' => 'Sanction Approved', 'step_order' => 13],
            ['name' => 'Disbursed', 'step_order' => 14],
        ];
        foreach ($statuses as $status) {
            DB::table('loan_status')->updateOrInsert(
                ['name' => $status['name']],   // unique key
                ['step_order' => $status['step_order']]
            );
        }
    }
}
