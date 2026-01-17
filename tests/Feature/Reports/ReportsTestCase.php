<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\Organization;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Company;
use App\Models\SaleTransaction;
use App\Models\PurchaseTransaction;
use App\Models\Batch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

abstract class ReportsTestCase extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected ?Organization $org1 = null;
    protected ?Organization $org2 = null;
    protected ?User $userOrg1 = null;
    protected ?User $userOrg2 = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setUpOrganizations();
    }

    /**
     * Get existing organizations and users from database
     */
    protected function setUpOrganizations(): void
    {
        // Get existing organizations
        $this->org1 = Organization::first();
        $this->org2 = Organization::skip(1)->first() ?? $this->org1;

        if (!$this->org1) {
            $this->markTestSkipped('No organizations found in database.');
            return;
        }

        // Get existing users for each organization
        $this->userOrg1 = User::where('organization_id', $this->org1->id)->first();
        $this->userOrg2 = User::where('organization_id', $this->org2->id)->first() ?? $this->userOrg1;

        if (!$this->userOrg1) {
            $this->markTestSkipped('No users found for testing.');
            return;
        }
    }

    /**
     * Create a sale transaction for testing
     */
    protected function createSaleTransaction(int $orgId, string $date, float $amount = 1000): ?SaleTransaction
    {
        $customer = Customer::where('organization_id', $orgId)->first();
        if (!$customer) return null;
        
        return SaleTransaction::create([
            'organization_id' => $orgId,
            'customer_id' => $customer->id,
            'invoice_no' => 'TEST-INV-' . uniqid(),
            'sale_date' => $date,
            'net_amount' => $amount,
            'taxable_amount' => $amount * 0.82,
            'cgst_amount' => $amount * 0.09,
            'sgst_amount' => $amount * 0.09,
            'status' => 'active',
        ]);
    }

    /**
     * Create a purchase transaction for testing
     */
    protected function createPurchaseTransaction(int $orgId, string $date, float $amount = 1000): ?PurchaseTransaction
    {
        $supplier = Supplier::where('organization_id', $orgId)->first();
        if (!$supplier) return null;
        
        return PurchaseTransaction::create([
            'organization_id' => $orgId,
            'supplier_id' => $supplier->id,
            'bill_no' => 'TEST-BILL-' . uniqid(),
            'bill_date' => $date,
            'net_amount' => $amount,
            'status' => 'active',
        ]);
    }

    /**
     * Get a formatted date string for testing
     */
    protected function getTestDate(int $daysAgo = 0): string
    {
        return now()->subDays($daysAgo)->format('Y-m-d');
    }

    /**
     * Assert response is successful (200 or redirect)
     */
    protected function assertResponseOk($response): void
    {
        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            "Expected status 200 or 302, got {$response->status()}"
        );
    }
}
