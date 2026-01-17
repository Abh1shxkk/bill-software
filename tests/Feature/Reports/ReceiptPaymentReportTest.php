<?php

namespace Tests\Feature\Reports;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * Receipt/Payment Report Tests (P1 - Priority)
 * 
 * Tests for receipt and payment reports including:
 * - Receipt from Customer
 * - Payment to Supplier
 * - Outstanding Reports
 * - Pay-in Slip
 */
class ReceiptPaymentReportTest extends ReportsTestCase
{
    /**
     * Test receipt from customer report renders
     */
    public function test_receipt_from_customer_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/receipt-from-customer');

        $response->assertStatus(200);
    }

    /**
     * Test receipt report with date filter
     */
    public function test_receipt_filters_by_date(): void
    {
        $this->createReceiptEntry($this->org1->id, 5000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/receipt-from-customer?view=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test payment to supplier report renders
     */
    public function test_payment_to_supplier_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/payment-to-supplier');

        $response->assertStatus(200);
    }

    /**
     * Test payment report with supplier filter
     */
    public function test_payment_filters_by_supplier(): void
    {
        $supplier = Supplier::where('organization_id', $this->org1->id)->first();
        $this->createPaymentEntry($this->org1->id, $supplier->id, 8000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/payment-to-supplier?view=1&supplier_id=' . $supplier->id . '&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test customer outstanding report
     */
    public function test_customer_outstanding_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/customer-outstanding');

        $response->assertStatus(200);
    }

    /**
     * Test supplier outstanding report
     */
    public function test_supplier_outstanding_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/supplier-outstanding');

        $response->assertStatus(200);
    }

    /**
     * Test pay-in slip report
     */
    public function test_pay_in_slip_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/pay-in-slip');

        $response->assertStatus(200);
    }

    /**
     * Test receipt print view
     */
    public function test_receipt_print_view(): void
    {
        $this->createReceiptEntry($this->org1->id, 12000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/receipt-from-customer?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test payment print view
     */
    public function test_payment_print_view(): void
    {
        $supplier = Supplier::where('organization_id', $this->org1->id)->first();
        $this->createPaymentEntry($this->org1->id, $supplier->id, 15000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/payment-to-supplier?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test receipt with customer filter
     */
    public function test_receipt_filters_by_customer(): void
    {
        $customer = Customer::where('organization_id', $this->org1->id)->first();
        
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/receipt-payment/receipt-from-customer?view=1&customer_id=' . $customer->id . '&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Helper: Create receipt entry
     */
    protected function createReceiptEntry(int $orgId, float $amount): void
    {
        $customer = Customer::where('organization_id', $orgId)->first();
        
        DB::table('customer_ledgers')->insert([
            'organization_id' => $orgId,
            'customer_id' => $customer->id,
            'transaction_date' => $this->getTestDate(),
            'transaction_type' => 'Receipt',
            'amount' => $amount,
            'narration' => 'Test Receipt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Helper: Create payment entry
     */
    protected function createPaymentEntry(int $orgId, int $supplierId, float $amount): void
    {
        DB::table('supplier_ledgers')->insert([
            'organization_id' => $orgId,
            'supplier_id' => $supplierId,
            'transaction_date' => $this->getTestDate(),
            'transaction_type' => 'Payment',
            'amount' => $amount,
            'narration' => 'Test Payment',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
