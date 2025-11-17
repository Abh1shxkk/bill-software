<?php

namespace Database\Seeders;

use App\Models\SaleLedger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SaleLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $ledgerNames = [
            'SALE - TAXPAID',
            'SALE CENTRAL FREE',
            'SALE GST 12%',
            'SALE GST 18%',
            'SALE GST 28%',
            'SALE GST 5%',
            'SALE GST FREE',
            'SALE IGST 12%',
            'SALE IGST 18%',
            'SALE IGST 28%',
            'SALE IGST 5%',
            'SALE IGST FREE',
        ];
        
        $types = ['L', 'C'];
        $formRequired = ['Y', 'N'];
        
        for ($i = 0; $i < 50; $i++) {
            SaleLedger::create([
                'ledger_name' => $faker->randomElement($ledgerNames),
                'form_type' => $faker->randomElement(['Invoice', 'Bill', 'Receipt', 'Quotation', '']),
                'sale_tax' => $faker->randomFloat(2, 0, 50),
                'desc' => $faker->sentence(),
                'type' => $faker->randomElement($types),
                'status' => $faker->randomElement(['ACTIVE', 'INACTIVE', 'PENDING', 'CLOSED', '']),
                'alter_code' => 'SL' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'opening_balance' => $faker->randomFloat(2, 0, 100000),
                'form_required' => $faker->randomElement($formRequired),
                'charges' => $faker->randomFloat(2, 0, 5000),
                'under' => $faker->randomElement(['SALE', 'INCOME', 'REVENUE', '']),
                'address' => $faker->address(),
                'birth_day' => $faker->optional()->date(),
                'anniversary' => $faker->optional()->date(),
                'telephone' => $faker->optional()->phoneNumber(),
                'fax' => $faker->optional()->phoneNumber(),
                'email' => $faker->optional()->email(),
                'contact_1' => $faker->optional()->name(),
                'mobile_1' => $faker->optional()->phoneNumber(),
                'contact_2' => $faker->optional()->name(),
                'mobile_2' => $faker->optional()->phoneNumber(),
            ]);
        }
    }
}
