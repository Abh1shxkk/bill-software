<?php

namespace Tests\Feature\Reports;

use App\Models\Item;
use App\Models\Batch;

/**
 * Inventory Report Tests (P0 - Priority)
 * 
 * Tests for inventory reports including:
 * - Stock Reports
 * - Expiry Reports
 * - Movement Reports
 */
class InventoryReportTest extends ReportsTestCase
{
    /**
     * Test stock report renders
     */
    public function test_stock_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/stock');

        $response->assertStatus(200);
    }

    /**
     * Test stock report shows only org items
     */
    public function test_stock_shows_only_org_items(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/stock?view=1');

        $response->assertStatus(200);
        $response->assertDontSee('BETA Item');
    }

    /**
     * Test expiry report renders
     */
    public function test_expiry_report_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/expiry');

        $response->assertStatus(200);
    }

    /**
     * Test near expiry report
     */
    public function test_near_expiry_report(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/near-expiry?days=30');

        $response->assertStatus(200);
    }

    /**
     * Test stock movement report
     */
    public function test_stock_movement_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/movement');

        $response->assertStatus(200);
    }

    /**
     * Test stock print view
     */
    public function test_stock_print_view(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/inventory/stock?print=1');

        $response->assertStatus(200);
    }
}
