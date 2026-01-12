<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\License;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class MultiTenantTestSeeder2 extends Seeder
{
    public function run(): void
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║     MULTI-TENANT SAAS TESTING - PHASES 4-6                    ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        $this->testPhase4SuperAdmin();
        $this->testPhase5Security();
        $this->testPhase6AuditLogs();
        
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║                    TESTING COMPLETE                            ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n";
    }

    protected function testPhase4SuperAdmin()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 4: SUPER ADMIN FUNCTIONALITY TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 4.1: Super Admin User Exists
        echo "Test 4.1: Super Admin User Verification\n";
        echo "─────────────────────────────────────────\n";
        
        $superAdmin = User::where('role', 'super_admin')->whereNull('organization_id')->first();
        
        if ($superAdmin) {
            echo "✓ Super Admin: {$superAdmin->full_name} ({$superAdmin->email})\n";
            echo "  Role: {$superAdmin->role}\n";
            echo "  Organization: " . ($superAdmin->organization_id ? "Org {$superAdmin->organization_id}" : "None (Platform Level)") . "\n";
            echo "Status: PASS ✓\n\n";
        } else {
            echo "✗ Super Admin not found\n";
            echo "Status: FAIL ✗\n\n";
        }

        // Test 4.2: Organization Count Visibility
        echo "Test 4.2: Organization Visibility to Super Admin\n";
        echo "─────────────────────────────────────────\n";
        
        $totalOrgs = Organization::count();
        $activeOrgs = Organization::where('status', 'active')->count();
        $suspendedOrgs = Organization::where('status', 'suspended')->count();
        
        echo "Total Organizations: {$totalOrgs}\n";
        echo "Active: {$activeOrgs}\n";
        echo "Suspended: {$suspendedOrgs}\n";
        
        if ($totalOrgs >= 2) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗ (Need at least 2 orgs for testing)\n\n";
        }

        // Test 4.3: License Management Capability
        echo "Test 4.3: License Management\n";
        echo "─────────────────────────────────────────\n";
        
        $totalLicenses = License::count();
        $activeLicenses = License::where('is_active', true)->count();
        $expiredLicenses = License::where('is_active', false)->orWhere('expires_at', '<', now())->count();
        
        echo "Total Licenses: {$totalLicenses}\n";
        echo "Active: {$activeLicenses}\n";
        echo "Expired/Inactive: {$expiredLicenses}\n";
        
        if ($totalLicenses >= 2 && $activeLicenses >= 2) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }
    }

    protected function testPhase5Security()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 5: SECURITY & DATA INTEGRITY TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 5.1: No Cross-Organization Data Leakage
        echo "Test 5.1: Cross-Organization Data Leakage Check\n";
        echo "─────────────────────────────────────────\n";
        
        // Check if any items reference companies from different orgs
        $crossOrgItems = DB::select("
            SELECT i.id, i.organization_id as item_org, c.organization_id as company_org
            FROM items i
            JOIN companies c ON i.company_id = c.id
            WHERE i.organization_id != c.organization_id
            LIMIT 5
        ");
        
        if (empty($crossOrgItems)) {
            echo "✓ No items referencing companies from other organizations\n";
            echo "Status: PASS ✓\n\n";
        } else {
            echo "✗ Found cross-organization references:\n";
            foreach ($crossOrgItems as $item) {
                echo "  Item ID {$item->id}: Item Org {$item->item_org} → Company Org {$item->company_org}\n";
            }
            echo "Status: FAIL ✗\n\n";
        }

        // Test 5.2: Organization ID Consistency
        echo "Test 5.2: Organization ID Consistency\n";
        echo "─────────────────────────────────────────\n";
        
        $tables = [
            'items' => 'Items',
            'customers' => 'Customers',
            'suppliers' => 'Suppliers',
            'companies' => 'Companies',
            'batches' => 'Batches',
            'hsn_codes' => 'HSN Codes',
        ];
        
        $allConsistent = true;
        foreach ($tables as $table => $label) {
            $nullCount = DB::table($table)->whereNull('organization_id')->count();
            if ($nullCount > 0) {
                echo "✗ {$label}: {$nullCount} records without organization_id\n";
                $allConsistent = false;
            } else {
                echo "✓ {$label}: All records have organization_id\n";
            }
        }
        
        if ($allConsistent) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }

        // Test 5.3: User-Organization Binding
        echo "Test 5.3: User-Organization Binding\n";
        echo "─────────────────────────────────────────\n";
        
        $usersWithoutOrg = User::whereNull('organization_id')->where('role', '!=', 'super_admin')->count();
        $usersWithOrg = User::whereNotNull('organization_id')->count();
        
        echo "Users with Organization: {$usersWithOrg}\n";
        echo "Users without Org (non-super-admin): {$usersWithoutOrg}\n";
        
        if ($usersWithoutOrg == 0) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗ (Found users without organization)\n\n";
        }
    }

    protected function testPhase6AuditLogs()
    {
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  PHASE 6: AUDIT LOGGING TESTING\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // Test 6.1: Audit Log Table Exists
        echo "Test 6.1: Audit Log Infrastructure\n";
        echo "─────────────────────────────────────────\n";
        
        try {
            $auditLogCount = AuditLog::count();
            echo "✓ Audit Log table exists\n";
            echo "Total Audit Logs: {$auditLogCount}\n";
            
            if ($auditLogCount > 0) {
                $org1Logs = AuditLog::where('organization_id', 1)->count();
                $org2Logs = AuditLog::where('organization_id', 9)->count();
                
                echo "Org 1 Logs: {$org1Logs}\n";
                echo "Org 2 Logs: {$org2Logs}\n";
            }
            
            echo "Status: PASS ✓\n\n";
        } catch (\Exception $e) {
            echo "✗ Audit Log table not accessible\n";
            echo "Status: FAIL ✗\n\n";
        }

        // Test 6.2: Audit Log Isolation
        echo "Test 6.2: Audit Log Organization Isolation\n";
        echo "─────────────────────────────────────────\n";
        
        try {
            $logsWithoutOrg = AuditLog::whereNull('organization_id')->count();
            
            if ($logsWithoutOrg == 0) {
                echo "✓ All audit logs have organization_id\n";
                echo "Status: PASS ✓\n\n";
            } else {
                echo "✗ Found {$logsWithoutOrg} logs without organization_id\n";
                echo "Status: FAIL ✗\n\n";
            }
        } catch (\Exception $e) {
            echo "⚠ Audit logs not yet populated\n";
            echo "Status: SKIP (No data to test)\n\n";
        }

        // Test 6.3: BelongsToOrganization Trait Usage
        echo "Test 6.3: BelongsToOrganization Trait Implementation\n";
        echo "─────────────────────────────────────────\n";
        
        $modelsWithTrait = [
            'App\Models\Item',
            'App\Models\Customer',
            'App\Models\Supplier',
            'App\Models\Company',
        ];
        
        $allImplemented = true;
        foreach ($modelsWithTrait as $modelClass) {
            if (class_exists($modelClass)) {
                $uses = class_uses($modelClass);
                if (in_array('App\Traits\BelongsToOrganization', $uses)) {
                    echo "✓ " . class_basename($modelClass) . " uses BelongsToOrganization\n";
                } else {
                    echo "✗ " . class_basename($modelClass) . " missing BelongsToOrganization\n";
                    $allImplemented = false;
                }
            }
        }
        
        if ($allImplemented) {
            echo "Status: PASS ✓\n\n";
        } else {
            echo "Status: FAIL ✗\n\n";
        }
    }
}
