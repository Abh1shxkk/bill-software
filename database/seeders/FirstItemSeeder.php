<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Company;
use App\Models\HsnCode;
use App\Models\Item;
use Illuminate\Database\Seeder;

class FirstItemSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization not found!');
            return;
        }

        $this->command->info('Seeding FIRST item from CSV...');

        $csvFile = base_path('item.xlsx - item.csv');
        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $lines = file($csvFile, FILE_IGNORE_NEW_LINES);
        
        // Line 1 is header
        // Line 2 is first data row
        if (count($lines) < 2) {
            $this->command->error('CSV file has no data!');
            return;
        }

        $header = str_getcsv($lines[0]);
        $data = str_getcsv($lines[1]);

        // Create associative array
        $row = array_combine($header, $data);

        $this->command->info('Processing item: ' . $row['name']);
        $this->command->info('Company code: ' . $row['Compcode']);
        $this->command->info('Company name: ' . $row['Compname']);
        $this->command->info('HSN Code: ' . ($row['HSNCode'] ?? 'N/A'));
        $this->command->info('CGST: ' . ($row['CGST'] ?? '0'));
        $this->command->info('SGST: ' . ($row['SGST'] ?? '0'));

        // Find company by name
        $company = Company::where('organization_id', $organization->id)
            ->where('name', trim($row['Compname']))
            ->first();

        if (!$company) {
            $this->command->error('Company not found: ' . $row['Compname']);
            $this->command->info('Creating company...');
            
            $company = Company::create([
                'name' => trim($row['Compname']),
                'organization_id' => $organization->id,
                'status' => 'Y',
                'is_deleted' => 0,
            ]);
        }

        $this->command->info('✓ Company found/created: ' . $company->name . ' (ID: ' . $company->id . ')');

        // Find or create HSN code if provided
        $hsnCodeString = null;
        if (!empty($row['HSNCode']) && $row['HSNCode'] !== '0') {
            $hsnCodeString = trim($row['HSNCode']);
        }

        // Helper function to clean numeric values
        $cleanNumeric = function($value) {
            if (is_null($value) || $value === '' || $value === 'N') {
                return null;
            }
            // Remove $ symbol and other non-numeric characters except decimal point and minus
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
            'organization_id' => $organization->id,
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

        $item = Item::updateOrCreate(
            [
                'name' => $itemData['name'],
                'company_id' => $company->id,
                'organization_id' => $organization->id
            ],
            $itemData
        );

        $this->command->info('');
        $this->command->info('✅ Successfully seeded first item!');
        $this->command->info('   Item: ' . $item->name);
        $this->command->info('   ID: ' . $item->id);
        $this->command->info('   Company: ' . $company->name);
        $this->command->info('   HSN Code: ' . ($hsnCodeString ? $hsnCodeString : 'N/A'));
    }
}
