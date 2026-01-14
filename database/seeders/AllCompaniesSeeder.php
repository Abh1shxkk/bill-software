<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AllCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization not found!');
            return;
        }

        $this->command->info('Seeding ALL companies from CSV...');

        $csvFile = base_path('COMPANY12.csv');
        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $lines = file($csvFile, FILE_IGNORE_NEW_LINES);
        $count = 0;

        // Skip header lines (first 5 lines)
        // Line 6 is: CODE,NAME,ADDRESS,TELEPHONE,EMAIL
        // Data starts from line 7
        
        for ($i = 6; $i < count($lines); $i++) {
            $line = $lines[$i];
            
            // Skip empty lines
            if (empty(trim($line))) {
                continue;
            }
            
            $data = str_getcsv($line);
            
            // Skip if no name (column 1)
            if (!isset($data[1]) || empty(trim($data[1]))) {
                continue;
            }

            $companyData = [
                'name' => trim($data[1]),
                'address' => isset($data[2]) && !empty(trim($data[2])) ? trim($data[2]) : null,
                'telephone' => isset($data[3]) && !empty(trim($data[3])) ? trim($data[3]) : null,
                'email' => isset($data[4]) && !empty(trim($data[4])) ? trim($data[4]) : null,
                'organization_id' => $organization->id,
                'status' => 'Y',
                'is_deleted' => 0,
            ];

            // Clean up character encoding
            foreach ($companyData as $key => $value) {
                if (is_string($value)) {
                    $value = preg_replace('/[\x{2018}\x{2019}]/u', "'", $value);
                    $value = preg_replace('/[\x{201C}\x{201D}]/u', '"', $value);
                    $value = preg_replace('/[\x{2013}\x{2014}]/u', '-', $value);
                    $companyData[$key] = $value;
                }
            }

            Company::updateOrCreate(
                [
                    'name' => $companyData['name'],
                    'organization_id' => $organization->id
                ],
                $companyData
            );

            $count++;
            
            if ($count % 10 == 0) {
                $this->command->info("✓ Processed $count companies...");
            }
        }

        $this->command->info('');
        $this->command->info("✅ Successfully seeded $count companies!");
    }
}
