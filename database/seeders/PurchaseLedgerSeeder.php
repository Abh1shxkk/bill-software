<?php

namespace Database\Seeders;

use App\Models\PurchaseLedger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PurchaseLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $ledgerNames = [
            'PURCHASE - TAX EXEMPTED',
            'PURCHASE - TAXABLE',
            'PURCHASE - IMPORT',
            'PURCHASE - LOCAL',
            'PURCHASE - WHOLESALE',
            'PURCHASE - RETAIL',
            'PURCHASE - DIRECT EXPENSES',
            'PURCHASE - INDIRECT EXPENSES',
        ];

        $statuses = ['Active', 'Inactive', 'Pending', 'Approved'];
        $types = ['L', 'C'];
        $formTypes = ['T', 'TR', 'PO', 'GRN'];
        $underCategories = [
            'PURCHASE & DIRECT EXPENSES',
            'INVENTORY',
            'COST OF GOODS SOLD',
            'OPERATING EXPENSES',
            'CAPITAL PURCHASES',
        ];

        for ($i = 0; $i < 15; $i++) {
            PurchaseLedger::create([
                'ledger_name' => $faker->randomElement($ledgerNames),
                'form_type' => $faker->randomElement($formTypes),
                'sale_tax' => $faker->randomFloat(2, 0, 18),
                'desc' => $faker->sentence(6),
                'type' => $faker->randomElement($types),
                'status' => $faker->randomElement($statuses),
                'alter_code' => strtoupper($faker->bothify('??###')),
                'opening_balance' => $faker->randomFloat(2, 1000, 100000),
                'form_required' => $faker->randomElement(['Y', 'N']),
                'charges' => $faker->randomFloat(2, 0, 5000),
                'under' => $faker->randomElement($underCategories),
                
                // Contact Information
                'address' => $faker->address(),
                'birth_day' => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'anniversary' => $faker->dateTimeBetween('-20 years', 'now')->format('Y-m-d'),
                'telephone' => $faker->phoneNumber(),
                'fax' => $faker->phoneNumber(),
                'email' => $faker->email(),
                'contact_1' => $faker->name(),
                'mobile_1' => $faker->numerify('98########'),
                'contact_2' => $faker->name(),
                'mobile_2' => $faker->numerify('97########'),
            ]);
        }
    }
}
