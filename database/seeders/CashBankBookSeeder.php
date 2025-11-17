<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashBankBook;

class CashBankBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'name' => 'CASH BOOK',
                'alter_code' => null,
                'under' => 'CASH IN HAND',
                'opening_balance' => 1401.30,
                'opening_balance_type' => 'D',
                'credit_card' => null,
                'bank_charges' => null,
                'address' => null,
                'address1' => null,
                'input_gst_purchase' => false,
                'output_gst_income' => false,
                'account_no' => null,
                'report_no' => null,
                'cheque_clearance_method' => 'P',
                'flag' => null,
                'receipts' => 'S',
                'transaction_date' => now(),
                'transaction_type' => 'Cash',
                'particulars' => 'Opening Balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ],
            [
                'name' => 'ELECTRICITY',
                'alter_code' => null,
                'under' => 'EXPENSE (INDIRECT)',
                'opening_balance' => 0.00,
                'opening_balance_type' => 'D',
                'credit_card' => 'N',
                'bank_charges' => 0.00,
                'address' => null,
                'address1' => null,
                'input_gst_purchase' => false,
                'output_gst_income' => false,
                'account_no' => null,
                'report_no' => null,
                'cheque_clearance_method' => 'P',
                'flag' => null,
                'receipts' => 'S',
                'transaction_date' => now(),
                'transaction_type' => 'Cash',
                'particulars' => 'Opening Balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ],
            [
                'name' => 'PUNJAB NATIONAL BANK',
                'alter_code' => null,
                'under' => 'CASH & BANK BALANCES',
                'opening_balance' => 36893975.00,
                'opening_balance_type' => 'D',
                'credit_card' => 'N',
                'bank_charges' => 0.00,
                'address' => 'MAIN BAZAR PRAHLAD NAGAR',
                'address1' => 'MEERUT - 250002',
                'input_gst_purchase' => false,
                'output_gst_income' => false,
                'account_no' => '04141131001026',
                'report_no' => null,
                'cheque_clearance_method' => 'I',
                'flag' => null,
                'receipts' => 'S',
                'transaction_date' => now(),
                'transaction_type' => 'Bank',
                'particulars' => 'Opening Balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ],
            [
                'name' => 'YES BANK',
                'alter_code' => null,
                'under' => 'CASH & BANK BALANCES',
                'opening_balance' => 24528602.00,
                'opening_balance_type' => 'D',
                'credit_card' => 'N',
                'bank_charges' => 0.00,
                'address' => 'PANCHSHEEL COLONEY GARH ROAD',
                'address1' => 'MEERUT - 250004',
                'input_gst_purchase' => false,
                'output_gst_income' => false,
                'account_no' => '0226619000001208',
                'report_no' => null,
                'cheque_clearance_method' => 'I',
                'flag' => null,
                'receipts' => 'S',
                'transaction_date' => now(),
                'transaction_type' => 'Bank',
                'particulars' => 'Opening Balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ],
            [
                'name' => 'YES BANK LIMITED',
                'alter_code' => null,
                'under' => 'CASH & BANK BALANCES',
                'opening_balance' => 0.00,
                'opening_balance_type' => 'D',
                'credit_card' => 'N',
                'bank_charges' => 0.00,
                'address' => 'GARH ROAD MEERUT',
                'address1' => null,
                'input_gst_purchase' => false,
                'output_gst_income' => false,
                'account_no' => '0226846000001651',
                'report_no' => null,
                'cheque_clearance_method' => 'P',
                'flag' => null,
                'receipts' => 'S',
                'transaction_date' => now(),
                'transaction_type' => 'Bank',
                'particulars' => 'Opening Balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
            ],
        ];

        foreach ($books as $book) {
            CashBankBook::create($book);
        }
    }
}
