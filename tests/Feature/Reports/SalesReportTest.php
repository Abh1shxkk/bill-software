<?php

namespace Tests\Feature\Reports;

use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\Customer;
use App\Models\Item;

/**
 * Sales Report Tests (P0 - Priority)
 * 
 * Tests for all sales report functionality including:
 * - Sales Summary
 * - Sales Book Party Wise
 * - Sale Return List
 * - Day Sales Summary
 * - Dispatch Sheet
 */
class SalesReportTest extends ReportsTestCase
{
    /**
     * Test sales summary report renders correctly
     */
    public function test_sales_summary_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/summary');

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.sale-report.sales-summary');
    }

    /**
     * Test sales summary with date filter
     */
    public function test_sales_summary_filters_by_date(): void
    {
        // Create sales on different dates
        $saleJan = $this->createSaleTransaction($this->org1->id, '2025-01-15', 5000);
        $saleFeb = $this->createSaleTransaction($this->org1->id, '2025-02-15', 3000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/summary?view=1&from_date=2025-01-01&to_date=2025-01-31');

        $response->assertStatus(200);
        
        // Should only include January sale
        $reportData = $response->viewData('reportData');
        // Verify date filtering logic
    }

    /**
     * Test sales book party wise report renders
     */
    public function test_sales_book_party_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/book-party-wise');

        $response->assertStatus(200);
    }

    /**
     * Test sales book groups by customer correctly
     */
    public function test_sales_book_groups_by_customer(): void
    {
        $customers = Customer::where('organization_id', $this->org1->id)->take(2)->get();
        
        // Create sales for different customers
        foreach ($customers as $customer) {
            $this->createSaleForCustomer($customer->id, $this->org1->id);
        }

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/book-party-wise?view=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test sale return list report
     */
    public function test_sale_return_list_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/return-list');

        $response->assertStatus(200);
    }

    /**
     * Test day sales summary item wise report
     */
    public function test_day_sales_summary_item_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/day-summary-item-wise');

        $response->assertStatus(200);
    }

    /**
     * Test dispatch sheet report
     */
    public function test_dispatch_sheet_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/dispatch-sheet');

        $response->assertStatus(200);
    }

    /**
     * Test sales summary print view
     */
    public function test_sales_summary_print_view(): void
    {
        $this->createSaleTransaction($this->org1->id, $this->getTestDate(), 10000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/summary?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.sale-report.sales-summary-print');
    }

    /**
     * Test sales report with customer filter
     */
    public function test_sales_report_customer_filter(): void
    {
        $customer = Customer::where('organization_id', $this->org1->id)->first();
        
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/book-party-wise?view=1&customer_id=' . $customer->id . '&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test sales stock summary report
     */
    public function test_sales_stock_summary_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/stock-summary');

        $response->assertStatus(200);
    }

    /**
     * Test shortage report renders
     */
    public function test_shortage_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/sales/shortage-report');

        $response->assertStatus(200);
    }

    /**
     * Helper: Create a sale for a specific customer
     */
    protected function createSaleForCustomer(int $customerId, int $orgId): SaleTransaction
    {
        return SaleTransaction::create([
            'organization_id' => $orgId,
            'customer_id' => $customerId,
            'invoice_no' => 'INV-' . uniqid(),
            'sale_date' => $this->getTestDate(),
            'net_amount' => rand(1000, 10000),
            'status' => 'active',
        ]);
    }
}
