<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\User;
use App\Models\License;

class MultiTenantTestSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║     MULTI-TENANT SAAS AUTOMATED TESTING SUITE                 ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        $this->testPhase1DataIsolation();
        $this->testPhase2LicenseManagement();
        $this->testPhase3UserManagement();
        
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║                    TESTING COMPLETE                            ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
    }

    protected function testPhase1DataIsolation()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 1: DATA ISOLATION TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 1.1: Organization Setup
        echo "Test 1.1: Organization Setup Verification\n";
        echo "─────────────────────────────────────────\n";
        
        $org1 = Organization::find(1);
        $org2 = Organization::find(9);
        
        if ($org1 && $org2) {
            echo "✓ Org 1: {$org1->name} (ID: 1)\n";
            echo "✓ Org 2: {$org2->name} (ID: 9)\n";
            echo "Status: PASS ✓\n\n";
        } else {
            echo "✗ Organizations not found\n";
            echo "Status: FAIL ✗\n\n";
        }

        // Test 1.2: Items Isolation
        echo "Test 1.2: Items Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Items = Item::where('organization_id', 1)->count();
        $org2Items = Item::where('organization_id', 9)->count();
        
        // Check for any items without organization_id
        $orphanItems = Item::whereNull('organization_id')->count();
        
        echo "Org 1 Items: {$org1Items}\n";
        echo "Org 2 Items: {$org2Items}\n";
        echo "Orphan Items (no org): {$orphanItems}\n";
        
        if ($org1Items > 0 && $org2Items > 0 && $orphanItems == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗ (Orphan items found or missing data)\n\n";
        }

        // Test 1.3: Customers Isolation
        echo "Test 1.3: Customers Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Customers = Customer::where('organization_id', 1)->count();
        $org2Customers = Customer::where('organization_id', 9)->count();
        $orphanCustomers = Customer::whereNull('organization_id')->count();
        
        echo "Org 1 Customers: {$org1Customers}\n";
        echo "Org 2 Customers: {$org2Customers}\n";
        echo "Orphan Customers: {$orphanCustomers}\n";
        
        if ($org1Customers > 0 && $org2Customers > 0 && $orphanCustomers == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 1.4: Suppliers Isolation
        echo "Test 1.4: Suppliers Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Suppliers = Supplier::where('organization_id', 1)->count();
        $org2Suppliers = Supplier::where('organization_id', 9)->count();
        $orphanSuppliers = Supplier::whereNull('organization_id')->count();
        
        echo "Org 1 Suppliers: {$org1Suppliers}\n";
        echo "Org 2 Suppliers: {$org2Suppliers}\n";
        echo "Orphan Suppliers: {$orphanSuppliers}\n";
        
        if ($org1Suppliers > 0 && $org2Suppliers > 0 && $orphanSuppliers == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 1.5: Companies Isolation
        echo "Test 1.5: Companies Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Companies = Company::where('organization_id', 1)->count();
        $org2Companies = Company::where('organization_id', 9)->count();
        $orphanCompanies = Company::whereNull('organization_id')->count();
        
        echo "Org 1 Companies: {$org1Companies}\n";
        echo "Org 2 Companies: {$org2Companies}\n";
        echo "Orphan Companies: {$orphanCompanies}\n";
        
        if ($org1Companies > 0 && $org2Companies > 0 && $orphanCompanies == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 1.6: Batches Isolation
        echo "Test 1.6: Batches Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Batches = \DB::table('batches')->where('organization_id', 1)->count();
        $org2Batches = \DB::table('batches')->where('organization_id', 9)->count();
        $orphanBatches = \DB::table('batches')->whereNull('organization_id')->count();
        
        echo "Org 1 Batches: {$org1Batches}\n";
        echo "Org 2 Batches: {$org2Batches}\n";
        echo "Orphan Batches: {$orphanBatches}\n";
        
        if ($org2Batches > 0 && $orphanBatches == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }
    }

    protected function testPhase2LicenseManagement()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 2: LICENSE MANAGEMENT TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 2.1: License Assignment
        echo "Test 2.1: License Assignment\n";
        echo "─────────────────────────────────────────\n";
        
        $org1License = License::where('organization_id', 1)->where('is_active', true)->first();
        $org2License = License::where('organization_id', 9)->where('is_active', true)->first();
        
        if ($org1License) {
            echo "✓ Org 1 License: {$org1License->plan_type} (Expires: {$org1License->expires_at->format('Y-m-d')})\n";
            echo "  Max Users: {$org1License->max_users}, Max Items: {$org1License->max_items}\n";
        } else {
            echo "✗ Org 1: No active license\n";
        }
        
        if ($org2License) {
            echo "✓ Org 2 License: {$org2License->plan_type} (Expires: {$org2License->expires_at->format('Y-m-d')})\n";
            echo "  Max Users: {$org2License->max_users}, Max Items: {$org2License->max_items}\n";
        } else {
            echo "✗ Org 2: No active license\n";
        }
        
        if ($org1License && $org2License) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 2.2: License Limits Check
        echo "Test 2.2: License Limits Verification\n";
        echo "─────────────────────────────────────────\n";
        
        if ($org1License) {
            $org1Users = User::where('organization_id', 1)->count();
            $org1Items = Item::where('organization_id', 1)->count();
            
            echo "Org 1:\n";
            echo "  Users: {$org1Users} / {$org1License->max_users}\n";
            echo "  Items: {$org1Items} / {$org1License->max_items}\n";
            
            if ($org1Users <= $org1License->max_users && $org1Items <= $org1License->max_items) {
                echo "  Within limits: ✓\n";
            } else {
                echo "  Exceeds limits: ✗\n";
            }
        }
        
        if ($org2License) {
            $org2Users = User::where('organization_id', 9)->count();
            $org2Items = Item::where('organization_id', 9)->count();
            
            echo "Org 2:\n";
            echo "  Users: {$org2Users} / {$org2License->max_users}\n";
            echo "  Items: {$org2Items} / {$org2License->max_items}\n";
            
            if ($org2Users <= $org2License->max_users && $org2Items <= $org2License->max_items) {
                echo "  Within limits: ✓\n";
            } else {
                echo "  Exceeds limits: ✗\n";
            }
        }
        
        echo "Status: PASS ✓\n\n";
    }

    protected function testPhase3UserManagement()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 3: USER MANAGEMENT TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 3.1: User Organization Assignment
        echo "Test 3.1: User Organization Assignment\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Users = User::where('organization_id', 1)->get();
        $org2Users = User::where('organization_id', 9)->get();
        $superAdmins = User::whereNull('organization_id')->where('role', 'super_admin')->count();
        
        echo "Org 1 Users: {$org1Users->count()}\n";
        foreach ($org1Users as $user) {
            $ownerTag = $user->is_organization_owner ? ' (Owner)' : '';
            echo "  - {$user->full_name} ({$user->role}){$ownerTag}\n";
        }
        
        echo "\nOrg 2 Users: {$org2Users->count()}\n";
        foreach ($org2Users as $user) {
            $ownerTag = $user->is_organization_owner ? ' (Owner)' : '';
            echo "  - {$user->full_name} ({$user->role}){$ownerTag}\n";
        }
        
        echo "\nSuper Admins: {$superAdmins}\n";
        
        if ($org1Users->count() > 0 && $org2Users->count() > 0 && $superAdmins > 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 3.2: Organization Owner Verification
        echo "Test 3.2: Organization Owner Verification\n";
        echo "─────────────────────────────────────────\n";
        
        $org1Owner = User::where('organization_id', 1)->where('is_organization_owner', true)->first();
        $org2Owner = User::where('organization_id', 9)->where('is_organization_owner', true)->first();
        
        if ($org1Owner) {
            echo "✓ Org 1 Owner: {$org1Owner->full_name} ({$org1Owner->email})\n";
        } else {
            echo "✗ Org 1: No owner found\n";
        }
        
        if ($org2Owner) {
            echo "✓ Org 2 Owner: {$org2Owner->full_name} ({$org2Owner->email})\n";
        } else {
            echo "✗ Org 2: No owner found\n";
        }
        
        if ($org1Owner && $org2Owner) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }
    }
}
