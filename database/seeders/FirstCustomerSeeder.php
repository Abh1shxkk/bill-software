<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class FirstCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Demo Organization
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization with code DEMO2026 not found!');
            $this->command->info('Please run OrganizationAdminSeeder first.');
            return;
        }

        $this->command->info('Seeding FIRST customer for: ' . $organization->name);

        // First customer data from CSV
        $customerData = [
            'code' => null,
            'name' => 'A S PHARMA',
            'address' => 'SHOP NO.12, FIRST FLOOR PRACHI COMPLEX, KHAIR NAGAR MEERUT',
            'mobile' => '08791118948',
            'telephone_office' => null,
            'telephone_residence' => null,
            'email' => null,
            'dl_number' => 'UP1520B002190',
            'dl_number1' => 'UP1521B002183',
            'gst_number' => '09ICMPK5757G1ZX',
            'pan_number' => 'ICMPK5757G',
            'organization_id' => $organization->id,
            'status' => 'Y',
            'is_deleted' => 0,
        ];

        $customer = Customer::updateOrCreate(
            [
                'name' => $customerData['name'],
                'organization_id' => $organization->id
            ],
            $customerData
        );

        $this->command->info('');
        $this->command->info('✅ First customer created successfully!');
        $this->command->info('Customer: ' . $customer->name);
        $this->command->info('Code: ' . ($customer->code ?? 'N/A'));
        $this->command->info('Mobile: ' . ($customer->mobile ?? 'N/A'));
        $this->command->info('GST: ' . ($customer->gst_number ?? 'N/A'));
    }
}
