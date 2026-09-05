<?php

namespace Database\Seeders;

use App\Models\Bank;
use DOMDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $jsonFile = database_path('seeders/banklist.json');

        if (!file_exists($jsonFile)) {
            $this->command->error('banklist.json file not found.');
            return;
        }

        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON: ' . json_last_error_msg());
            return;
        }

        $institutions = $data['institutions']['institution'] ?? [];

        if (empty($institutions)) {
            $this->command->error('No institutions found in banklist.json.');
            return;
        }

        $count = 0;

        foreach ($institutions as $institution) {

            Bank::updateOrCreate(
                [
                    'institute_id' => $institution['id'],
                ],
                [
                    'name' => $institution['name'],
                    'nick_name' => $institution['nickName'],
                    'active' => 1,
                    'branch_name' => 'NA',
                    'ifsc_code' => 'NA',
                    'logo_url' => 'NA',
                    'website' => 'NA',
                    'category' => 'NA',
                    'bank_code' => 'NA',

                ]
            );

            $count++;
        }

        $this->command->info("Imported {$count} banks successfully.");
    }
}
