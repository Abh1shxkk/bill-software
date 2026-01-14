<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class AllCustomersSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization not found!');
            return;
        }

        $this->command->info('Seeding ALL customers from CSV...');

        $csvFile = base_path('customer12.csv');
        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $lines = file($csvFile, FILE_IGNORE_NEW_LINES);
        $currentCustomer = null;
        $count = 0;
        $lineIndex = 0;

        foreach ($lines as $line) {
            $lineIndex++;
            $data = str_getcsv($line);
            
            // Skip header rows
            if ($lineIndex <= 4 || empty(trim($line))) {
                continue;
            }

            // Check if this is a new customer record (has S.NO in first column)
            if (isset($data[0]) && is_numeric(trim($data[0]))) {
                // Save previous customer if exists
                if ($currentCustomer !== null && !empty($currentCustomer['name'])) {
                    $this->createCustomer($organization, $currentCustomer);
                    $count++;
                    
                    if ($count % 50 == 0) {
                        $this->command->info("✓ Processed $count customers...");
                    }
                }

                // Start new customer
                $currentCustomer = [
                    'code' => isset($data[1]) && !empty(trim($data[1])) ? trim($data[1]) : null,
                    'name' => isset($data[2]) && !empty(trim($data[2])) ? trim($data[2]) : null,
                    'mobile' => null,
                    'telephone_office' => null,
                    'address' => '',
                    'dl_number' => null,
                    'dl_number1' => null,
                    'gst_number' => null,
                    'pan_number' => null,
                    'email' => null,
                ];

                // Check if mobile is on the same line
                if (isset($data[3]) && strpos($data[3], 'Mob :') !== false) {
                    $mobData = trim(str_replace('Mob :', '', $data[3]));
                    $currentCustomer['mobile'] = !empty($mobData) ? $mobData : null;
                }
            } else {
                // Continuation lines for current customer
                if ($currentCustomer !== null) {
                    // Column 2 usually has address lines
                    if (isset($data[2]) && !empty(trim($data[2]))) {
                        $addressLine = trim($data[2]);
                        if (!empty($addressLine)) {
                            if (!empty($currentCustomer['address'])) {
                                $currentCustomer['address'] .= ', ' . $addressLine;
                            } else {
                                $currentCustomer['address'] = $addressLine;
                            }
                        }
                    }

                    // Column 3 has various fields
                    if (isset($data[3]) && !empty(trim($data[3]))) {
                        $fieldData = trim($data[3]);
                        
                        if (strpos($fieldData, 'Mob :') !== false || strpos($fieldData, 'Mob:') !== false) {
                            $mobData = trim(preg_replace('/Mob\s*:\s*/', '', $fieldData));
                            $currentCustomer['mobile'] = !empty($mobData) ? $mobData : null;
                        } 
                        elseif (strpos($fieldData, 'Tel :') !== false || strpos($fieldData, 'Tel:') !== false) {
                            $telData = trim(preg_replace('/Tel\s*:\s*/', '', $fieldData));
                            $currentCustomer['telephone_office'] = !empty($telData) ? $telData : null;
                        }
                        elseif (strpos($fieldData, 'DL.NO.') !== false || strpos($fieldData, 'DL.NO') !== false) {
                            $dlData = trim(preg_replace('/DL\.NO\.\s*:\s*/', '', $fieldData));
                            $dlData = trim(preg_replace('/DL\.NO\s*:\s*/', '', $dlData));
                            $dlParts = array_map('trim', explode(',', $dlData));
                            $currentCustomer['dl_number'] = isset($dlParts[0]) && !empty($dlParts[0]) ? $dlParts[0] : null;
                            $currentCustomer['dl_number1'] = isset($dlParts[1]) && !empty($dlParts[1]) ? $dlParts[1] : null;
                        }
                        elseif (strpos($fieldData, 'GstNo') !== false) {
                            $gstData = trim(preg_replace('/GstNo\s*:\s*/', '', $fieldData));
                            $currentCustomer['gst_number'] = !empty($gstData) ? $gstData : null;
                        }
                        elseif (strpos($fieldData, 'PAN') !== false) {
                            $panData = trim(preg_replace('/PAN\s*:\s*/', '', $fieldData));
                            $currentCustomer['pan_number'] = !empty($panData) ? $panData : null;
                        }
                        elseif (strpos($fieldData, 'Email') !== false) {
                            $emailData = trim(preg_replace('/Email\s*:\s*/', '', $fieldData));
                            $currentCustomer['email'] = !empty($emailData) ? $emailData : null;
                        }
                    }
                }
            }
        }

        // Save last customer
        if ($currentCustomer !== null && !empty($currentCustomer['name'])) {
            $this->createCustomer($organization, $currentCustomer);
            $count++;
        }

        $this->command->info('');
        $this->command->info("✅ Successfully seeded $count customers!");
    }

    private function createCustomer($organization, $data)
    {
        $data['organization_id'] = $organization->id;
        $data['status'] = 'Y';
        $data['is_deleted'] = 0;

        // Clean up empty address
        if (empty(trim($data['address']))) {
            $data['address'] = null;
        }

        // Clean up character encoding issues
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Replace problematic smart quotes and special characters
                $value = preg_replace('/[\x{2018}\x{2019}]/u', "'", $value); // Smart single quotes
                $value = preg_replace('/[\x{201C}\x{201D}]/u', '"', $value); // Smart double quotes
                $value = preg_replace('/[\x{2013}\x{2014}]/u', '-', $value); // En/em dashes
                $data[$key] = $value;
            }
        }

        Customer::updateOrCreate(
            [
                'name' => $data['name'],
                'organization_id' => $organization->id
            ],
            $data
        );
    }
}
