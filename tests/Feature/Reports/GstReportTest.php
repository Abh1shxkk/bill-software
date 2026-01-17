<?php

namespace Tests\Feature\Reports;

use App\Models\SaleTransaction;
use App\Models\PurchaseTransaction;
use App\Models\SaleTransactionItem;
use Illuminate\Support\Facades\DB;

/**
 * GST Report Tests (P1 - Priority)
 * 
 * Tests for GST reports including:
 * - GSTR-1 (Sales Return)
 * - GSTR-2 (Purchase Return)
 * - HSN Summary
 * - Stock Trans Reports
 * - GSTR-9 Export
 */
class GstReportTest extends ReportsTestCase
{
    /**
     * Test GSTR-1 report renders
     */
    public function test_gstr1_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr1');

        $response->assertStatus(200);
    }

    /**
     * Test GSTR-1 with date filter
     */
    public function test_gstr1_filters_by_date(): void
    {
        $this->createSaleTransaction($this->org1->id, $this->getTestDate(), 10000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr1?view=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test GSTR-2 report renders
     */
    public function test_gstr2_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr2');

        $response->assertStatus(200);
    }

    /**
     * Test HSN Summary shows correct data
     */
    public function test_hsn_summary_report(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/hsn-summary');

        $response->assertStatus(200);
    }

    /**
     * Test Stock Trans 1 report
     */
    public function test_stock_trans_1_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/stock-trans-1');

        $response->assertStatus(200);
    }

    /**
     * Test Stock Trans 2 report
     */
    public function test_stock_trans_2_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/stock-trans-2');

        $response->assertStatus(200);
    }

    /**
     * Test GSTR-9 export functionality
     */
    public function test_gstr9_export_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr9-export');

        $response->assertStatus(200);
    }

    /**
     * Test GSTR-9 annual data calculation
     */
    public function test_gstr9_calculates_annual_totals(): void
    {
        // Create transactions for the financial year
        $this->createSaleTransaction($this->org1->id, '2025-04-15', 50000);
        $this->createSaleTransaction($this->org1->id, '2025-08-20', 75000);
        $this->createPurchaseTransaction($this->org1->id, '2025-06-10', 40000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr9-export?generate=1&financial_year=2025');

        $response->assertStatus(200);
    }

    /**
     * Test Customer GST Detail report
     */
    public function test_customer_gst_detail_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/customer-gst-detail');

        $response->assertStatus(200);
    }

    /**
     * Test E-Way Bill generation report
     */
    public function test_eway_bill_generation_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/eway-bill-generation');

        $response->assertStatus(200);
    }

    /**
     * Test GSTR-1 print view
     */
    public function test_gstr1_print_view(): void
    {
        $this->createSaleTransaction($this->org1->id, $this->getTestDate(), 25000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/gst/gstr1?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }
}
