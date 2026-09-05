<?php

namespace Database\Seeders;

use App\Models\CommonDocument;
use Illuminate\Database\Seeder;

class CommonDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'name' => 'Aadhaar Card',
                'variable_name' => 'aadhaar',
            ],
            [
                'name' => 'PAN Card',
                'variable_name' => 'pan',
            ],
            [
                'name' => 'Drive License',
                'variable_name' => 'driving_license',
            ],
            [
                'name' => 'Voter ID',
                'variable_name' => 'voter_id',
            ],
            [
                'name' => 'Passport',
                'variable_name' => 'passport',
            ],
        ];

        foreach ($documents as $document) {
            CommonDocument::updateOrCreate(
                ['name' => $document['name']],
                ['variable_name' => $document['variable_name']]
            );
        }
    }
}
