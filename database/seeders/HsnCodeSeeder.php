<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\HsnCode;
use Illuminate\Database\Seeder;

class HsnCodeSeeder extends Seeder
{
    public function run(): void
    {
        // Target: DEMO2026 organization
        $targetOrg = Organization::where('code', 'DEMO2026')->first();

        if (!$targetOrg) {
            $this->command->error('Target organization (DEMO2026) not found!');
            return;
        }

        $this->command->info('Creating missing tax rate variations for HSN codes...');

        // Standard tax rates: 0%, 5%, 12%, 18%
        $taxRates = [
            ['cgst' => 0.00, 'sgst' => 0.00, 'igst' => 0.00, 'total' => 0.00],
            ['cgst' => 2.50, 'sgst' => 2.50, 'igst' => 5.00, 'total' => 5.00],
            ['cgst' => 6.00, 'sgst' => 6.00, 'igst' => 12.00, 'total' => 12.00],
            ['cgst' => 9.00, 'sgst' => 9.00, 'igst' => 18.00, 'total' => 18.00],
        ];

        // Get all unique HSN codes for DEMO2026
        $uniqueHsnCodes = HsnCode::where('organization_id', $targetOrg->id)
            ->where('name', 'DRUGS AND MEDICINES')
            ->distinct()
            ->pluck('hsn_code');

        $this->command->info("Found {$uniqueHsnCodes->count()} unique HSN codes");

        $created = 0;
        $existing = 0;

        foreach ($uniqueHsnCodes as $hsnCode) {
            // Get a sample record to copy other attributes
            $sample = HsnCode::where('organization_id', $targetOrg->id)
                ->where('hsn_code', $hsnCode)
                ->first();

            // Create all 4 tax rate variations
            foreach ($taxRates as $rate) {
                $exists = HsnCode::where('organization_id', $targetOrg->id)
                    ->where('hsn_code', $hsnCode)
                    ->where('name', 'DRUGS AND MEDICINES')
                    ->where('cgst_percent', $rate['cgst'])
                    ->where('sgst_percent', $rate['sgst'])
                    ->exists();

                if (!$exists) {
                    HsnCode::create([
                        'organization_id' => $targetOrg->id,
                        'hsn_code' => $hsnCode,
                        'name' => 'DRUGS AND MEDICINES',
                        'cgst_percent' => $rate['cgst'],
                        'sgst_percent' => $rate['sgst'],
                        'igst_percent' => $rate['igst'],
                        'total_gst_percent' => $rate['total'],
                        'is_inactive' => $sample->is_inactive ?? false,
                        'is_service' => $sample->is_service ?? false,
                    ]);
                    $created++;
                } else {
                    $existing++;
                }
            }
        }

        $this->command->info('');
        $this->command->info("✅ Created $created new tax rate variations");
        $this->command->info("✅ Found $existing existing variations");
        
        $total = HsnCode::where('organization_id', $targetOrg->id)
            ->where('name', 'DRUGS AND MEDICINES')
            ->count();
        $this->command->info("✅ Total HSN codes: $total");
    }
}
