<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Company;

class ItemDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Legacy items data (paste your Excel data here in array format)
        $legacyItems = [
            [
                'code' => 1,
                'Barcode' => '00001',
                'name' => 'PIOLET M 15mg',
                'Compcode' => 1,
                'Compname' => 'INTAS',
                'Pack' => '1*10',
                'Unit' => 1,
                'Location' => '',
                'Expiry' => 'N',
                'Generic' => 'N',
                'Prate' => 42.50,
                'Srate' => 42.50,
                'Wsrate' => 0.00,
                'Costrate' => 38.25,
                'splrate' => '$0.00',
                'Mrp' => '$55.20',
                'WsNet' => 'N',
                'SplNet' => 'N',
                'Hscm' => 'N',
                'QScm' => 'N',
                'Scm1' => 0,
                'scm2' => 0,
                'Division' => 'SUPRIMA',
                'Box' => 0,
                'ItCase' => 0,
                'MinQty' => 0,
                'MaxQty' => 0,
                'UnitType' => 'M',
                'MinGP' => 0,
                'HSNCode' => '',
                'CGST' => 0,
                'SGST' => 0,
                'IGST' => 0,
                'GSTCess' => 0,
                'VAT' => 4,
                'FDis' => 'N',
                'FDisP' => 0,
                'currentScm' => 'N',
                'ScmFrom' => '10-Aug-02',
                'ScmTo' => '10-Aug-02',
                'Inclusive' => 'N',
                'IWeight' => 0,
                'Vol' => 0,
                'cname_bc' => 'Y',
                'Defqty' => 'N',
                'MfgBy' => '',
            ],
            // Add more items here...
        ];

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($legacyItems as $legacy) {
            try {
                // Find company by name (Compname)
                $company = Company::where('name', 'LIKE', '%' . trim($legacy['Compname']) . '%')
                    ->orWhere('short_name', 'LIKE', '%' . trim($legacy['Compname']) . '%')
                    ->first();

                if (!$company) {
                    $errors[] = "Company not found: {$legacy['Compname']} for item: {$legacy['name']}";
                    $errorCount++;
                    continue;
                }

                // Clean and map data
                $itemData = $this->mapLegacyToNew($legacy, $company);

                // Use updateOrCreate to avoid duplicates
                Item::updateOrCreate(
                    ['name' => $itemData['name']], // match by name
                    $itemData
                );

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Error processing item {$legacy['name']}: " . $e->getMessage();
                $errorCount++;
            }
        }

        $this->command->info("✅ Successfully seeded {$successCount} items!");
        
        if ($errorCount > 0) {
            $this->command->warn("⚠️ {$errorCount} items had errors:");
            foreach ($errors as $error) {
                $this->command->error("  - {$error}");
            }
        }
    }

    /**
     * Map legacy Excel data to new database fields
     */
    private function mapLegacyToNew(array $legacy, Company $company): array
    {
        return [
            // Company relationship
            'company_id' => $company->id,
            'company_short_name' => $company->short_name ?? substr($company->name, 0, 20),
            
            // Basic Info
            'name' => trim($legacy['name']),
            'packing' => $legacy['Pack'] ?? null,
            'mfg_by' => !empty($legacy['MfgBy']) ? $legacy['MfgBy'] : null,
            'location' => !empty($legacy['Location']) ? $legacy['Location'] : null,
            'status' => null, // varchar(5) - too small for 'Active'
            'schedule' => '00',
            
            // Quantities
            'box_qty' => (int)($legacy['Box'] ?? 0),
            'case_qty' => (int)($legacy['ItCase'] ?? 0),
            
            // Codes
            'bar_code' => $legacy['Barcode'] ?? null,
            'division' => $this->truncate($legacy['Division'] ?? '00', 10), // varchar(10)
            'flag' => null,
            
            // Unit Info
            'unit' => (int)($legacy['Unit'] ?? 1),
            'unit_type' => $legacy['UnitType'] ?? null,
            
            // Min/Max Levels
            'min_level' => $this->parseDecimal($legacy['MinQty'] ?? 0),
            'max_level' => $this->parseDecimal($legacy['MaxQty'] ?? 0),
            
            // Flags
            'narcotic_flag' => 'N',
            'expiry_flag' => $this->parseFlag($legacy['Expiry'] ?? 'N'),
            'inclusive_flag' => $this->parseFlag($legacy['Inclusive'] ?? 'N'),
            'generic_flag' => $this->parseFlag($legacy['Generic'] ?? 'N'),
            'h_scm_flag' => $this->parseFlag($legacy['Hscm'] ?? 'N'),
            'q_scm_flag' => $this->parseFlag($legacy['QScm'] ?? 'N'),
            'locks_flag' => 'N',
            'bar_code_flag' => 'N',
            'def_qty_flag' => $this->parseFlag($legacy['Defqty'] ?? 'N'),
            'dpc_item_flag' => 'N',
            'lock_sale_flag' => 'N',
            'current_scheme_flag' => $this->parseFlag($legacy['currentScm'] ?? 'N'),
            
            // Rates (clean $ signs)
            's_rate' => $this->parseDecimal($legacy['Srate'] ?? 0),
            'mrp' => $this->parseDecimal($legacy['Mrp'] ?? 0),
            'ws_rate' => $this->parseDecimal($legacy['Wsrate'] ?? 0),
            'ws_net_toggle' => $this->parseFlag($legacy['WsNet'] ?? 'N'),
            'spl_rate' => $this->parseDecimal($legacy['splrate'] ?? 0),
            'spl_net_toggle' => $this->parseFlag($legacy['SplNet'] ?? 'N'),
            
            // Scheme
            'scheme_plus' => (int)($legacy['Scm1'] ?? 0),
            'scheme_minus' => (int)($legacy['scm2'] ?? 0),
            'sale_scheme' => null,
            
            // Purchase
            'min_gp' => $this->parseDecimal($legacy['MinGP'] ?? 0),
            'pur_rate' => $this->parseDecimal($legacy['Prate'] ?? 0),
            'cost' => $this->parseDecimal($legacy['Costrate'] ?? 0),
            'pur_scheme_plus' => 0,
            'pur_scheme_minus' => 0,
            'pur_scheme' => null,
            'nr' => 0,
            
            // Tax
            'hsn_code' => !empty($legacy['HSNCode']) ? $legacy['HSNCode'] : null,
            'cgst_percent' => $this->parseDecimal($legacy['CGST'] ?? 0),
            'sgst_percent' => $this->parseDecimal($legacy['SGST'] ?? 0),
            'igst_percent' => $this->parseDecimal($legacy['IGST'] ?? 0),
            'cess_percent' => $this->parseDecimal($legacy['GSTCess'] ?? 0),
            'vat_percent' => $this->parseDecimal($legacy['VAT'] ?? 0),
            
            // Fixed Discount
            'fixed_dis' => $this->parseFlag($legacy['FDis'] ?? 'N'),
            'fixed_dis_percent' => $this->parseDecimal($legacy['FDisP'] ?? 0),
            'fixed_dis_type' => null,
            
            // Additional fields
            'max_inv_qty_value' => 0,
            'max_inv_qty_new' => null,
            'weight_new' => $this->parseDecimal($legacy['IWeight'] ?? 0),
            'volume_new' => $this->parseDecimal($legacy['Vol'] ?? 0),
            'comp_name_bc_new' => $this->parseFlag($legacy['cname_bc'] ?? 'N'),
            'max_min_flag' => '1',
            'mrp_for_sale_new' => 0,
            'commodity' => null,
            
            // Scheme dates
            'from_date' => $this->parseDate($legacy['ScmFrom'] ?? null),
            'to_date' => $this->parseDate($legacy['ScmTo'] ?? null),
            'scheme_plus_value' => 0,
            'scheme_minus_value' => 0,
            
            // Categories
            'category' => null,
            'category_2' => null,
            'upc' => null,
            
            // System
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Parse decimal values, removing $ signs and other non-numeric characters
     */
    private function parseDecimal($value): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        
        // Remove $ signs and other non-numeric chars except . and -
        $cleaned = preg_replace('/[^0-9.\-]/', '', (string)$value);
        
        return $cleaned !== '' ? (float)$cleaned : 0.00;
    }

    /**
     * Parse flag values (Y/N)
     */
    private function parseFlag($value): string
    {
        $val = strtoupper(trim((string)$value));
        return in_array($val, ['Y', 'YES', '1', 'TRUE']) ? 'Y' : 'N';
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Try parsing common date formats
            $formats = ['d-M-y', 'd-M-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try PHP's strtotime
            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            // Return null if parsing fails
        }

        return null;
    }

    /**
     * Truncate string to max length
     */
    private function truncate(?string $value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }
        return substr($value, 0, $maxLength);
    }
}
