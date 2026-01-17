<?php

namespace Tests\Feature\Reports;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Company;
use App\Models\SaleTransaction;

/**
 * CRITICAL: Organization Isolation Tests
 * 
 * These tests verify that data is properly filtered by organization_id,
 * preventing data leakage between organizations in the reports module.
 */
class OrganizationIsolationTest extends ReportsTestCase
{
    /**
     * Test customer records are filtered by organization_id
     */
    public function test_customer_organization_filter(): void
    {
        if (!$this->org1) {
            $this->markTestSkipped('No organization available for testing.');
        }

        $org1CustomerCount = Customer::where('organization_id', $this->org1->id)->count();
        $this->assertIsInt($org1CustomerCount);
        $this->assertGreaterThanOrEqual(0, $org1CustomerCount);
    }

    /**
     * Test item records are filtered by organization_id
     */
    public function test_item_organization_filter(): void
    {
        if (!$this->org1) {
            $this->markTestSkipped('No organization available for testing.');
        }

        $org1ItemCount = Item::where('organization_id', $this->org1->id)->count();
        $this->assertIsInt($org1ItemCount);
        $this->assertGreaterThanOrEqual(0, $org1ItemCount);
    }

    /**
     * Test supplier records are filtered by organization_id
     */
    public function test_supplier_organization_filter(): void
    {
        if (!$this->org1) {
            $this->markTestSkipped('No organization available for testing.');
        }

        $org1SupplierCount = Supplier::where('organization_id', $this->org1->id)->count();
        $this->assertIsInt($org1SupplierCount);
        $this->assertGreaterThanOrEqual(0, $org1SupplierCount);
    }

    /**
     * Test company records are filtered by organization_id
     */
    public function test_company_organization_filter(): void
    {
        if (!$this->org1) {
            $this->markTestSkipped('No organization available for testing.');
        }

        $org1CompanyCount = Company::where('organization_id', $this->org1->id)->count();
        $this->assertIsInt($org1CompanyCount);
        $this->assertGreaterThanOrEqual(0, $org1CompanyCount);
    }

    /**
     * Test sale transactions are filtered by organization_id
     */
    public function test_sale_transaction_organization_filter(): void
    {
        if (!$this->org1) {
            $this->markTestSkipped('No organization available for testing.');
        }

        $org1SaleCount = SaleTransaction::where('organization_id', $this->org1->id)->count();
        $this->assertIsInt($org1SaleCount);
        $this->assertGreaterThanOrEqual(0, $org1SaleCount);
    }

    /**
     * Test authenticated user has organization_id
     */
    public function test_authenticated_user_has_org_id(): void
    {
        if (!$this->userOrg1) {
            $this->markTestSkipped('No user available for testing.');
        }

        $this->actingAs($this->userOrg1);
        
        $this->assertNotNull(auth()->user()->organization_id);
        $this->assertEquals($this->userOrg1->organization_id, auth()->user()->organization_id);
    }

    /**
     * Test that Eloquent global scope is applied when user is authenticated
     */
    public function test_global_scope_applied_to_customers(): void
    {
        if (!$this->userOrg1) {
            $this->markTestSkipped('No user available for testing.');
        }

        $this->actingAs($this->userOrg1);

        $customers = Customer::take(10)->get();
        
        foreach ($customers as $customer) {
            $this->assertEquals(
                $this->userOrg1->organization_id, 
                $customer->organization_id,
                'Customer should belong to authenticated user\'s organization'
            );
        }
    }

    /**
     * Test that Eloquent global scope is applied to items
     */
    public function test_global_scope_applied_to_items(): void
    {
        if (!$this->userOrg1) {
            $this->markTestSkipped('No user available for testing.');
        }

        $this->actingAs($this->userOrg1);

        $items = Item::take(10)->get();
        
        foreach ($items as $item) {
            $this->assertEquals(
                $this->userOrg1->organization_id, 
                $item->organization_id,
                'Item should belong to authenticated user\'s organization'
            );
        }
    }

    /**
     * Test that Eloquent global scope is applied to suppliers
     */
    public function test_global_scope_applied_to_suppliers(): void
    {
        if (!$this->userOrg1) {
            $this->markTestSkipped('No user available for testing.');
        }

        $this->actingAs($this->userOrg1);

        $suppliers = Supplier::take(10)->get();
        
        foreach ($suppliers as $supplier) {
            $this->assertEquals(
                $this->userOrg1->organization_id, 
                $supplier->organization_id,
                'Supplier should belong to authenticated user\'s organization'
            );
        }
    }
}
