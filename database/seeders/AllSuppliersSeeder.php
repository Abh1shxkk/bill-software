<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class AllSuppliersSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization not found!');
            return;
        }

        $this->command->info('Seeding ALL suppliers from CSV...');

        $csvFile = base_path('supplier12.csv');
        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $handle = fopen($csvFile, 'r');
        $currentSupplier = [];
        $count = 0;
        $skipFirst = true;

        while (($line = fgets($handle)) !== false) {
            $data = str_getcsv($line);
            
            // Skip header rows
            if (isset($data[0]) && (strpos($data[0], 'PRABHAT') !== false || strpos($data[0], 'SUPPLIER LIST') !== false || $data[0] === 'S.NO')) {
                continue;
            }

            // Check if this is a new supplier (has S.NO)
            if (isset($data[0]) && is_numeric($data[0])) {
                // Save previous supplier if exists
                if (!empty($currentSupplier['name']) && !$skipFirst) {
                    $this->createSupplier($organization, $currentSupplier);
                    $count++;
                }
                
                if ($skipFirst) {
                    $skipFirst = false;
                    $currentSupplier = [];
                    continue;
                }

                // Start new supplier
                $currentSupplier = [
                    'code' => isset($data[1]) && !empty(trim($data[1])) ? trim($data[1]) : null,
                    'name' => isset($data[2]) ? trim($data[2]) : null,
                    'mobile' => null,
                    'telephone' => null,
                    'address' => '',
                    'dl_no' => null,
                    'dl_no_1' => null,
                    'gst_no' => null,
                    'email' => null,
                ];
            } else {
                // Continuation of current supplier data
                if (!empty($currentSupplier)) {
                    $line = trim($line);
                    
                    if (strpos($line, 'Mob :') !== false) {
                        $currentSupplier['mobile'] = trim(str_replace('Mob :', '', $data[3] ?? ''));
                    } elseif (strpos($line, 'Tel :') !== false) {
                        $currentSupplier['telephone'] = trim(str_replace(['Tel :', 'Tel:'], '', $data[3] ?? ''));
                    } elseif (strpos($line, 'DL.NO.') !== false || strpos($line, 'DL.NO') !== false) {
                        $dlData = trim(str_replace(['DL.NO. :', 'DL.NO. ', 'DL.NO:', 'DL.NO '], '', $data[3] ?? ''));
                        $dlParts = explode(',', $dlData);
                        $currentSupplier['dl_no'] = isset($dlParts[0]) ? trim($dlParts[0]) : null;
                        $currentSupplier['dl_no_1'] = isset($dlParts[1]) ? trim($dlParts[1]) : null;
                    } elseif (strpos($line, 'GstNo') !== false || strpos($line, 'GstNo') !== false) {
                        $currentSupplier['gst_no'] = trim(str_replace(['GstNo :', 'GstNo:', 'GstNo '], '', $data[3] ?? ''));
                    } elseif (strpos($line, 'Email') !== false || strpos($line, 'Email:') !== false) {
                        $currentSupplier['email'] = trim(str_replace(['Email:', 'Email '], '', $data[3] ?? ''));
                        if (empty($currentSupplier['email'])) $currentSupplier['email'] = null;
                    } else {
                        // Address line
                        if (isset($data[2]) && !empty(trim($data[2]))) {
                            $currentSupplier['address'] .= ($currentSupplier['address'] ? ', ' : '') . trim($data[2]);
                        }
                    }
                }
            }

            if ($count > 0 && $count % 20 == 0) {
                $this->command->info("✓ Processed $count suppliers...");
            }
        }

        // Save last supplier
        if (!empty($currentSupplier['name'])) {
            $this->createSupplier($organization, $currentSupplier);
            $count++;
        }

        fclose($handle);

        $this->command->info('');
        $this->command->info("✅ Successfully seeded $count suppliers!");
    }

    private function createSupplier($organization, $data)
    {
        $data['organization_id'] = $organization->id;
        $data['status'] = 'Y';
        $data['is_deleted'] = 0;

        Supplier::updateOrCreate(
            [
                'name' => $data['name'],
                'organization_id' => $organization->id
            ],
            $data
        );
    }
}
