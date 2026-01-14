<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Company;
use App\Models\HsnCode;
use App\Models\Item;
use Illuminate\Database\Seeder;

class AllItemsSeeder extends Seeder
{
    private $organization;
    private $companyCache = [];
    private $hsnCodeCache = [];
    private $stats = [
        'total' => 0,
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];
    private $skippedItems = [];
    private $errorItems = [];

    public function run(): void
    {
        $this->organization = Organization::where('code', 'DEMO2026')->first();

        if (!$this->organization) {
            $this->command->error('Organization not found!');
            return;
        }

        $this->command->info('Seeding ALL items from CSV in chunks of 1000...');
        $this->command->info('');

        $csvFile = base_path('item.xlsx - item.csv');
        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $lines = file($csvFile, FILE_IGNORE_NEW_LINES);
        
        // Line 1 is header
        if (count($lines) < 2) {
            $this->command->error('CSV file has no data!');
            return;
        }

        $header = str_getcsv($lines[0]);
        $totalLines = count($lines) - 1; // Exclude header
        
        $this->command->info("Total items to process: $totalLines");
        $this->command->info('');

        // Pre-load companies into cache
        $this->loadCompanyCache();
        
        // Process in chunks of 1000
        $chunkSize = 1000;
        $currentChunk = 1;
        
        for ($i = 1; $i < count($lines); $i += $chunkSize) {
            $chunkEnd = min($i + $chunkSize, count($lines));
            $chunkCount = $chunkEnd - $i;
            
            $this->command->info("Processing chunk $currentChunk (items " . ($i) . " to " . ($chunkEnd - 1) . ")...");
            
            for ($j = $i; $j < $chunkEnd; $j++) {
                $line = $lines[$j];
                
                // Skip empty lines
                if (empty(trim($line))) {
                    $this->stats['skipped']++;
                    $this->skippedItems[] = "Line $j: Empty line";
                    continue;
                }
                
                try {
                    $data = str_getcsv($line);
                    
                    // Skip if no name
                    if (!isset($data[2]) || empty(trim($data[2]))) {
                        $this->stats['skipped']++;
                        $this->skippedItems[] = "Line $j: No item name";
                        continue;
                    }
                    
                    $row = array_combine($header, $data);
                    $this->processItem($row);
                    
                } catch (\Exception $e) {
                    $this->stats['errors']++;
                    $this->errorItems[] = "Line $j: " . $e->getMessage();
                    $this->command->error("Error on line $j: " . $e->getMessage());
                }
            }
            
            $this->command->info("✓ Chunk $currentChunk completed");
            $this->command->info("  Created: {$this->stats['created']}, Updated: {$this->stats['updated']}, Skipped: {$this->stats['skipped']}, Errors: {$this->stats['errors']}");
            $this->command->info('');
            
            $currentChunk++;
        }

        $this->command->info('');
        $this->command->info('=== SEEDING COMPLETED ===');
        $this->command->info("✅ Total processed: {$this->stats['total']}");
        $this->command->info("✅ Created: {$this->stats['created']}");
        $this->command->info("✅ Updated: {$this->stats['updated']}");
        $this->command->info("✅ Skipped: {$this->stats['skipped']}");
        $this->command->info("✅ Errors: {$this->stats['errors']}");
        
        if (count($this->skippedItems) > 0) {
            $this->command->info('');
            $this->command->info('=== SKIPPED ITEMS ===');
            foreach ($this->skippedItems as $item) {
                $this->command->warn($item);
            }
        }
        
        if (count($this->errorItems) > 0) {
            $this->command->info('');
            $this->command->info('=== ERROR ITEMS ===');
            foreach ($this->errorItems as $item) {
                $this->command->error($item);
            }
        }
    }

    private function loadCompanyCache(): void
    {
        $this->command->info('Loading companies into cache...');
        
        $companies = Company::where('organization_id', $this->organization->id)->get();
        
        foreach ($companies as $company) {
            $this->companyCache[strtoupper(trim($company->name))] = $company;
        }
        
        $this->command->info("✓ Loaded " . count($this->companyCache) . " companies");
        $this->command->info('');
    }

    private function getOrCreateCompany(string $companyName): ?Company
    {
        $key = strtoupper(trim($companyName));
        
        if (isset($this->companyCache[$key])) {
            return $this->companyCache[$key];
        }
        
        // Create new company
        $company = Company::create([
            'name' => trim($companyName),
            'organization_id' => $this->organization->id,
            'status' => 'Y',
            'is_deleted' => 0,
        ]);
        
        $this->companyCache[$key] = $company;
        
        return $company;
    }

    private function getOrCreateHsnCode(string $hsnCode, float $cgst, float $sgst, float $igst): ?string
    {
        // Return the HSN code string directly, not the ID
        return $hsnCode;
    }

    private function processItem(array $row): void
    {
        $this->stats['total']++;
        
        // Get or create company
        $company = $this->getOrCreateCompany($row['Compname']);
        
        if (!$company) {
            $this->stats['skipped']++;
            return;
        }

        // Get or create HSN code if provided
        $hsnCodeString = null;
        if (!empty($row['HSNCode']) && $row['HSNCode'] !== '0') {
            $cgst = !empty($row['CGST']) ? floatval($row['CGST']) : 0;
            $sgst = !empty($row['SGST']) ? floatval($row['SGST']) : 0;
            $igst = !empty($row['IGST']) ? floatval($row['IGST']) : 0;

            $hsnCodeString = $this->getOrCreateHsnCode($row['HSNCode'], $cgst, $sgst, $igst);
        }

        // Helper function to clean numeric values
        $cleanNumeric = function($value) {
            if (is_null($value) || $value === '' || $value === 'N') {
                return null;
            }
            $cleaned = preg_replace('/[^0-9.-]/', '', $value);
            return $cleaned !== '' ? $cleaned : null;
        };

        // Helper function to clean boolean values
        $cleanBoolean = function($value) {
            if ($value === 'Y' || $value === 'y' || $value === '1' || $value === 1) {
                return 1;
            }
            return 0;
        };

        // Map CSV fields to database fields
        $itemData = [
            'organization_id' => $this->organization->id,
            'company_id' => $company->id,
            'company_short_name' => trim($row['Compname']),
            
            // Basic Information
            'name' => trim($row['name']),
            'packing' => !empty($row['Pack']) ? trim($row['Pack']) : null,
            'unit' => !empty($row['Unit']) ? trim($row['Unit']) : null,
            'location' => !empty($row['Location']) ? trim($row['Location']) : null,
            'status' => 'Y', // Active status
            'bar_code' => !empty($row['Barcode']) ? trim($row['Barcode']) : null,
            'division' => !empty($row['Division']) ? trim($row['Division']) : null,
            'mfg_by' => !empty($row['MfgBy']) ? trim($row['MfgBy']) : null,
            
            // Sale Details
            's_rate' => $cleanNumeric($row['Srate']),
            'mrp' => $cleanNumeric($row['Mrp']),
            'ws_rate' => $cleanNumeric($row['Wsrate']),
            'spl_rate' => $cleanNumeric($row['splrate']),
            'scheme_plus' => $cleanNumeric($row['Scm1']),
            'scheme_minus' => $cleanNumeric($row['scm2']),
            'min_gp' => $cleanNumeric($row['MinGP']),
            
            // Purchase Details
            'pur_rate' => $cleanNumeric($row['Prate']),
            'cost' => $cleanNumeric($row['Costrate']),
            'pur_scheme_plus' => $cleanNumeric($row['Scm1']),
            'pur_scheme_minus' => $cleanNumeric($row['scm2']),
            
            // GST Details
            'hsn_code' => $hsnCodeString,
            'cgst_percent' => $cleanNumeric($row['CGST']),
            'sgst_percent' => $cleanNumeric($row['SGST']),
            'igst_percent' => $cleanNumeric($row['IGST']),
            'cess_percent' => $cleanNumeric($row['GSTCess']),
            
            // Other Details
            'vat_percent' => $cleanNumeric($row['VAT']),
            'fixed_dis' => $cleanNumeric($row['FDis']),
            'fixed_dis_percent' => $cleanNumeric($row['FDisP']),
            'expiry_flag' => $cleanBoolean($row['Expiry']),
            'inclusive_flag' => $cleanBoolean($row['Inclusive']),
            'generic_flag' => $cleanBoolean($row['Generic']),
            'box_qty' => $cleanNumeric($row['Box']),
            'case_qty' => $cleanNumeric($row['ItCase']),
            'min_level' => $cleanNumeric($row['MinQty']),
            'max_level' => $cleanNumeric($row['MaxQty']),
            
            // Flags and other fields
            'flag' => !empty($row['Flag']) ? trim($row['Flag']) : null,
            'unit_type' => !empty($row['UnitType']) ? trim($row['UnitType']) : null,
            'category' => !empty($row['ItemCat']) ? trim($row['ItemCat']) : null,
            
            // System fields
            'is_deleted' => 0,
        ];

        // Clean up character encoding
        foreach ($itemData as $key => $value) {
            if (is_string($value)) {
                $value = preg_replace('/[\x{2018}\x{2019}]/u', "'", $value);
                $value = preg_replace('/[\x{201C}\x{201D}]/u', '"', $value);
                $value = preg_replace('/[\x{2013}\x{2014}]/u', '-', $value);
                $itemData[$key] = $value;
            }
        }

        // Check if item exists
        $existingItem = Item::where('name', $itemData['name'])
            ->where('company_id', $company->id)
            ->where('organization_id', $this->organization->id)
            ->first();

        if ($existingItem) {
            $existingItem->update($itemData);
            $this->stats['updated']++;
        } else {
            Item::create($itemData);
            $this->stats['created']++;
        }
    }
}
