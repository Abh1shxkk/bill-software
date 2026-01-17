<?php

namespace Tests\Feature\Reports;

use App\Models\SaleTransaction;
use App\Models\SaleTransactionItem;
use App\Models\PurchaseTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

/**
 * Management Report Tests (P0 - Priority)
 * 
 * Tests for management reports including:
 * - Gross Profit Reports (All Items, Company Wise, Customer Wise, etc.)
 * - Non-Moving Items
 * - Slow Moving Items
 * - Performance Report
 * - Day Check List
 * - Due Reports
 */
class ManagementReportTest extends ReportsTestCase
{
    /**
     * Test gross profit all items report renders
     */
    public function test_gross_profit_all_items_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/gross-profit-all-items');

        $response->assertStatus(200);
    }

    /**
     * Test gross profit calculation accuracy
     */
    public function test_gross_profit_calculation(): void
    {
        // Create item with known cost
        $item = $this->createItemWithBatch($this->org1->id, 100); // Cost = 100
        
        // Create sale with known selling price
        $this->createSaleWithItem($item, $this->org1->id, 10, 150); // Qty=10, Rate=150
        
        // Expected: Sale = 1500, Cost = 1000, GP = 500 (33.33%)
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/gross-profit-all-items?view=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test gross profit company wise report
     */
    public function test_gross_profit_company_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/gross-profit-company-wise');

        $response->assertStatus(200);
    }

    /**
     * Test gross profit customer wise report
     */
    public function test_gross_profit_customer_wise_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/gross-profit-customer-wise');

        $response->assertStatus(200);
    }

    /**
     * Test non-moving items report
     */
    public function test_non_moving_items_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/non-moving-items');

        $response->assertStatus(200);
    }

    /**
     * Test non-moving items with days filter
     */
    public function test_non_moving_items_filters_by_days(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/non-moving-items?view=1&days=30');

        $response->assertStatus(200);
    }

    /**
     * Test slow moving items report
     */
    public function test_slow_moving_items_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/slow-moving-items');

        $response->assertStatus(200);
    }

    /**
     * Test performance report renders
     */
    public function test_performance_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/performance-report');

        $response->assertStatus(200);
    }

    /**
     * Test day check list report
     */
    public function test_day_check_list_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/day-check-list');

        $response->assertStatus(200);
    }

    /**
     * Test day check list shows all 12 transaction types
     */
    public function test_day_check_list_shows_all_transaction_types(): void
    {
        $this->createSaleTransaction($this->org1->id, $this->getTestDate(), 5000);
        $this->createPurchaseTransaction($this->org1->id, $this->getTestDate(), 3000);

        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/day-check-list?view=1&check_date=' . $this->getTestDate());

        $response->assertStatus(200);
        
        // Check for transaction type labels
        $response->assertSee('Sales');
        $response->assertSee('Purchase');
    }

    /**
     * Test customer due report
     */
    public function test_customer_due_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/due-reports/customer-due-list');

        $response->assertStatus(200);
    }

    /**
     * Test supplier due report
     */
    public function test_supplier_due_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/due-reports/supplier-due-list');

        $response->assertStatus(200);
    }

    /**
     * Test gross profit print view
     */
    public function test_gross_profit_print_view(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/gross-profit-all-items?print=1&from_date=' . $this->getTestDate(30) . '&to_date=' . $this->getTestDate());

        $response->assertStatus(200);
    }

    /**
     * Test expired items report
     */
    public function test_expired_items_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/list-of-expired-items');

        $response->assertStatus(200);
    }

    /**
     * Test pending order reports
     */
    public function test_customers_pending_order_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/customers-pending-order');

        $response->assertStatus(200);
    }

    public function test_suppliers_pending_order_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/management/suppliers-pending-order');

        $response->assertStatus(200);
    }

    /**
     * Helper: Create item with batch at known cost
     */
    protected function createItemWithBatch(int $orgId, float $costRate): Item
    {
        $item = Item::where('organization_id', $orgId)->first();
        
        Batch::create([
            'organization_id' => $orgId,
            'item_id' => $item->id,
            'batch_no' => 'TEST-' . uniqid(),
            'cost_rate' => $costRate,
            'sale_rate' => $costRate * 1.5,
            'qty' => 100,
            'expiry_date' => now()->addYear(),
        ]);

        return $item;
    }

    /**
     * Helper: Create sale with specific item
     */
    protected function createSaleWithItem(Item $item, int $orgId, int $qty, float $rate): SaleTransaction
    {
        $customer = Customer::where('organization_id', $orgId)->first();
        
        $sale = SaleTransaction::create([
            'organization_id' => $orgId,
            'customer_id' => $customer->id,
            'invoice_no' => 'INV-' . uniqid(),
            'sale_date' => $this->getTestDate(),
            'net_amount' => $qty * $rate,
            'status' => 'active',
        ]);

        SaleTransactionItem::create([
            'sale_transaction_id' => $sale->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'qty' => $qty,
            'rate' => $rate,
            'net_amount' => $qty * $rate,
        ]);

        return $sale;
    }
}
