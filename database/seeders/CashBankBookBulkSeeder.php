<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashBankBook;

class CashBankBookBulkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creditCardOptions = ['Y', 'N', 'W'];
        $clearanceOptions = ['P', 'I'];
        $receiptsOptions = ['S', 'I'];
        $transactionTypes = ['Bank', 'Cash'];
        $underOptions = ['CASH & BANK BALANCES', 'CASH IN HAND', 'EXPENSE (INDIRECT)', 'BANK CHARGES'];

        for ($i = 1; $i <= 50; $i++) {
            CashBankBook::create([
                'name' => 'BANK_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'alter_code' => 'CODE-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'under' => $underOptions[array_rand($underOptions)],
                'opening_balance' => rand(100000, 50000000),
                'opening_balance_type' => 'D',
                'credit_card' => $creditCardOptions[array_rand($creditCardOptions)],
                'bank_charges' => rand(100, 1000),
                'address' => 'ADDRESS LOCATION ' . $i . ', CITY ' . $i,
                'address1' => 'POSTAL CODE ' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'input_gst_purchase' => (bool) rand(0, 1),
                'output_gst_income' => (bool) rand(0, 1),
                'account_no' => str_repeat(str_pad($i, 2, '0', STR_PAD_LEFT), 8),
                'report_no' => null,
                'cheque_clearance_method' => $clearanceOptions[array_rand($clearanceOptions)],
                'flag' => null,
                'receipts' => $receiptsOptions[array_rand($receiptsOptions)],
                'transaction_date' => now(),
                'transaction_type' => $transactionTypes[array_rand($transactionTypes)],
                'particulars' => 'Opening Balance Entry ' . $i,
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ]);
        }
    }
}
