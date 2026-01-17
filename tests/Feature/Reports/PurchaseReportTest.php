<?php

namespace Tests\Feature\Reports;

use App\Models\PurchaseTransaction;
use App\Models\Supplier;

/**
 * Purchase Report Tests (P1 - Priority)
 * 
 * Tests for purchase reports including:
 * - Purchase Book (Bill-wise, Party-wise, Item-wise)
 * - Purchase Return List
 * - GST Set-Off
 * - Supplier-wise Summary
 */
class PurchaseReportTest extends ReportsTestCase
{
    /**
     * Test party wise purchase report renders
     */
    public function test_party_wise_purchase_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/party-wise');

        $response->assertStatus(200);
    }

    /**
     * Test party wise purchase with date filter
     */
    public function test_party_wise_purchase_filters_by_date(): void
    {
        $this->createPurchaseTransaction($this->org1->id, $this->getTestDate(), 15000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/party-wise?view=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test purchase book bill wise
     */
    public function test_purchase_book_bill_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/book/bill-wise');

        $response->assertStatus(200);
    }

    /**
     * Test purchase return list
     */
    public function test_purchase_return_list_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/return-list');

        $response->assertStatus(200);
    }

    /**
     * Test purchase return item wise
     */
    public function test_purchase_return_item_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/return-item-wise');

        $response->assertStatus(200);
    }

    /**
     * Test day purchase summary
     */
    public function test_day_purchase_summary_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/day-summary');

        $response->assertStatus(200);
    }

    /**
     * Test monthly purchase summary
     */
    public function test_monthly_purchase_summary_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/monthly-summary');

        $response->assertStatus(200);
    }

    /**
     * Test local central register
     */
    public function test_local_central_register_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/local-central-register');

        $response->assertStatus(200);
    }

    /**
     * Test debit credit note report
     */
    public function test_debit_credit_note_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/debit-credit-note');

        $response->assertStatus(200);
    }

    /**
     * Test short expiry received report
     */
    public function test_short_expiry_received_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/short-expiry-received');

        $response->assertStatus(200);
    }

    /**
     * Test purchase voucher detail
     */
    public function test_purchase_voucher_detail_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/voucher-detail');

        $response->assertStatus(200);
    }

    /**
     * Test purchase report with supplier filter
     */
    public function test_purchase_report_supplier_filter(): void
    {
        $supplier = Supplier::where('organization_id', $this->org1->id)->first();
        
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/party-wise?view=1&supplier_id=' . $supplier->id . '&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test purchase report print view
     */
    public function test_purchase_report_print_view(): void
    {
        $this->createPurchaseTransaction($this->org1->id, $this->getTestDate(), 20000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/purchase/party-wise?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }
}
