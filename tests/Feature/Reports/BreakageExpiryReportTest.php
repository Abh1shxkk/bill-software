<?php

namespace Tests\Feature\Reports;

/**
 * Breakage/Expiry Report Tests (P1 - Priority)
 */
class BreakageExpiryReportTest extends ReportsTestCase
{
    public function test_from_customer_pending_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/breakage-expiry/from-customer/pending');
        $response->assertStatus(200);
    }

    public function test_from_customer_all_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/breakage-expiry/from-customer/all');
        $response->assertStatus(200);
    }

    public function test_to_supplier_pending_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/breakage-expiry/to-supplier/pending');
        $response->assertStatus(200);
    }

    public function test_to_supplier_all_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/breakage-expiry/to-supplier/all');
        $response->assertStatus(200);
    }

    public function test_replacement_to_customer_renders(): void
    {
        $response = $this->actingAs($this->userOrg1)
            ->get('/admin/reports/breakage-expiry/replacement/pending');
        $response->assertStatus(200);
    }
}
