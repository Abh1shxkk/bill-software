<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // 1. Create Organization
        $orgId = DB::table('organizations')->insertGetId([
            'name' => 'Sharma Pharma Distributors',
            'code' => 'SPD-' . strtoupper(substr(uniqid(), -4)),
            'email' => 'sharma@pharmadist.com',
            'phone' => '9876543210',
            'address' => '45, Gandhi Road, Near Railway Station',
            'city' => 'Lucknow',
            'state' => 'Uttar Pradesh',
            'country' => 'India',
            'pin_code' => '226001',
            'gst_no' => '09AABCS1234H1ZP',
            'pan_no' => 'AABCS1234H',
            'dl_no' => 'UP/LKO/2024/001234',
            'dl_no_1' => 'UP/LKO/2024/001235',
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'date_format' => 'd-m-Y',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        echo "Created Organization ID: $orgId\n";

        // 2. Create Admin User
        $userId = DB::table('users')->insertGetId([
            'organization_id' => $orgId,
            'is_organization_owner' => true,
            'full_name' => 'Rajesh Sharma',
            'username' => 'sharma_admin',
            'email' => 'sharma@pharmadist.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'licensed_to' => 'Sharma Pharma Distributors',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        echo "Created Admin User ID: $userId\n";

        // 3. Create License
        DB::table('licenses')->insert([
            'organization_id' => $orgId,
            'license_key' => 'LIC-' . strtoupper(substr(md5(uniqid()), 0, 16)),
            'plan_type' => 'standard',
            'max_users' => 5,
            'max_items' => 5000,
            'is_active' => true,
            'activated_at' => $now,
            'expires_at' => $now->copy()->addYear(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        echo "Created License\n";

        // 4. Create Companies
        $companies = ['Sun Pharmaceutical', 'Cipla Limited', 'Dr. Reddy Labs', 'Mankind Pharma', 'Zydus Lifesciences'];

        $companyIds = [];
        foreach ($companies as $company) {
            $companyIds[] = DB::table('companies')->insertGetId([
                'organization_id' => $orgId,
                'name' => $company,
                'address' => 'Mumbai, Maharashtra',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "Created " . count($companyIds) . " Companies\n";

        // 5. Create Suppliers
        $suppliers = ['Metro Pharma Traders', 'Wellness Distributors', 'HealthCare Supplies'];

        $supplierIds = [];
        foreach ($suppliers as $i => $supplier) {
            $supplierIds[] = DB::table('suppliers')->insertGetId([
                'organization_id' => $orgId,
                'name' => $supplier,
                'code' => 'SUP' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'address' => 'Delhi, India',
                'telephone' => '98' . rand(10000000, 99999999),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "Created " . count($supplierIds) . " Suppliers\n";

        // 6. Create Customers
        $customers = ['City Medical Store', 'Apollo Pharmacy', 'Jan Aushadhi', 'LifeCare Hospital', 'Community Health'];

        $customerIds = [];
        foreach ($customers as $i => $customer) {
            $customerIds[] = DB::table('customers')->insertGetId([
                'organization_id' => $orgId,
                'name' => $customer,
                'code' => 'CUST' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'address' => 'Lucknow, UP',
                'city' => 'Lucknow',
                'mobile' => '91' . rand(10000000, 99999999),
                'created_date' => $now,
                'modified_date' => $now,
            ]);
        }

        echo "Created " . count($customerIds) . " Customers\n";

        // 7. Create HSN Codes
        $hsnCodes = [
            ['code' => '3004', 'name' => 'Medicaments'],
            ['code' => '30049099', 'name' => 'Other medicaments'],
            ['code' => '30042099', 'name' => 'Antibiotics'],
        ];

        foreach ($hsnCodes as $hsn) {
            DB::table('hsn_codes')->insertGetId([
                'organization_id' => $orgId,
                'name' => $hsn['name'],
                'hsn_code' => $hsn['code'],
                'cgst_percent' => 6,
                'sgst_percent' => 6,
                'igst_percent' => 12,
                'total_gst_percent' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        echo "Created 3 HSN Codes\n";

        // 8. Create Items
        $items = [
            ['name' => 'Paracetamol 500mg', 'pack' => '10 Tab', 'mrp' => 25, 'company' => 0],
            ['name' => 'Amoxicillin 500mg', 'pack' => '10 Cap', 'mrp' => 85, 'company' => 1],
            ['name' => 'Omeprazole 20mg', 'pack' => '10 Cap', 'mrp' => 65, 'company' => 2],
            ['name' => 'Metformin 500mg', 'pack' => '10 Tab', 'mrp' => 35, 'company' => 3],
            ['name' => 'Azithromycin 500mg', 'pack' => '3 Tab', 'mrp' => 95, 'company' => 4],
            ['name' => 'Cetirizine 10mg', 'pack' => '10 Tab', 'mrp' => 30, 'company' => 0],
            ['name' => 'Pantoprazole 40mg', 'pack' => '10 Tab', 'mrp' => 75, 'company' => 1],
            ['name' => 'Atorvastatin 10mg', 'pack' => '10 Tab', 'mrp' => 55, 'company' => 2],
            ['name' => 'Insulin Glargine', 'pack' => '1 Vial', 'mrp' => 850, 'company' => 3],
            ['name' => 'Chyawanprash 500g', 'pack' => '1 Jar', 'mrp' => 250, 'company' => 4],
        ];

        $itemCount = 0;
        $batchCount = 0;

        foreach ($items as $i => $item) {
            $itemId = DB::table('items')->insertGetId([
                'organization_id' => $orgId,
                'name' => $item['name'],
                'company_id' => $companyIds[$item['company']],
                'packing' => $item['pack'],
                'mrp' => $item['mrp'],
                's_rate' => $item['mrp'] * 0.9,
                'pur_rate' => $item['mrp'] * 0.7,
                'cgst_percent' => 6,
                'sgst_percent' => 6,
                'igst_percent' => 12,
                'hsn_code' => '3004',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $itemCount++;

            // Create 2 batches per item (using correct column names: pur_rate, s_rate)
            for ($b = 1; $b <= 2; $b++) {
                $expiry = $now->copy()->addMonths(rand(6, 24));
                DB::table('batches')->insert([
                    'organization_id' => $orgId,
                    'item_id' => $itemId,
                    'batch_no' => 'B' . date('ym') . str_pad($itemId, 3, '0', STR_PAD_LEFT) . $b,
                    'expiry_date' => $expiry,
                    'mrp' => $item['mrp'],
                    'pur_rate' => $item['mrp'] * 0.7,
                    's_rate' => $item['mrp'] * 0.9,
                    'qty' => rand(50, 200),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $batchCount++;
            }
        }

        echo "Created $itemCount Items with $batchCount Batches\n";

        // 9. Create a Staff User
        DB::table('users')->insert([
            'organization_id' => $orgId,
            'is_organization_owner' => false,
            'full_name' => 'Amit Kumar',
            'username' => 'amit_staff',
            'email' => 'amit@pharmadist.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        echo "Created Staff User\n";

        echo "\n=== DEMO ORGANIZATION CREATED ===\n";
        echo "Organization: Sharma Pharma Distributors\n";
        echo "Admin Login: sharma_admin / password123\n";
        echo "Staff Login: amit_staff / password123\n";
        echo "================================\n";
    }
}
