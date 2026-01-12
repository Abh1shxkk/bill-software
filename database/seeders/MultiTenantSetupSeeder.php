<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MultiTenantSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sets up the initial multi-tenant infrastructure.
     */
    public function run(): void
    {
        $this->command->info('Setting up multi-tenant infrastructure...');

        // 1. Create Super Admin user
        $this->createSuperAdmin();

        // 2. Create default organization for existing data
        $defaultOrg = $this->createDefaultOrganization();

        // 3. Assign existing users to default organization
        $this->assignExistingUsersToOrganization($defaultOrg);

        // 4. Assign existing data to default organization
        $this->assignExistingDataToOrganization($defaultOrg);

        $this->command->info('Multi-tenant setup completed successfully!');
    }

    /**
     * Create the super admin user
     */
    protected function createSuperAdmin(): void
    {
        $existingSuperAdmin = User::where('role', 'super_admin')->first();

        if ($existingSuperAdmin) {
            $this->command->info('Super Admin already exists: ' . $existingSuperAdmin->email);
            return;
        }

        $superAdmin = User::create([
            'full_name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'superadmin@medibill.com',
            'password' => Hash::make('SuperAdmin@123'),
            'role' => 'super_admin',
            'organization_id' => null, // Super admin has no organization
            'is_organization_owner' => false,
            'is_active' => true,
        ]);

        $this->command->info('Created Super Admin: ' . $superAdmin->email);
        $this->command->warn('Password: SuperAdmin@123 (Please change after first login!)');
    }

    /**
     * Create default organization for existing data
     */
    protected function createDefaultOrganization(): Organization
    {
        $existingOrg = Organization::where('code', 'DEFAULT')->first();

        if ($existingOrg) {
            $this->command->info('Default organization already exists.');
            return $existingOrg;
        }

        $organization = Organization::create([
            'name' => 'Default Organization',
            'code' => 'DEFAULT',
            'email' => 'admin@default.com',
            'status' => 'active',
            'country' => 'India',
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
        ]);

        // Create enterprise license for default organization (no expiry)
        $license = License::create([
            'organization_id' => $organization->id,
            'license_key' => 'DEFAULT-0000-0000-0000',
            'plan_type' => 'enterprise',
            'max_users' => 999,
            'max_items' => 999999,
            'max_transactions_per_month' => 999999,
            'issued_at' => now()->format('Y-m-d H:i:s'),
            'starts_at' => now()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addYears(100)->format('Y-m-d H:i:s'),
            'is_active' => true,
            'activated_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Default organization license - no expiry',
        ]);

        $this->command->info('Created default organization: ' . $organization->name);
        $this->command->info('License key: ' . $license->license_key);

        return $organization;
    }

    /**
     * Assign existing users to the default organization
     */
    protected function assignExistingUsersToOrganization(Organization $organization): void
    {
        $usersWithoutOrg = User::whereNull('organization_id')
            ->where('role', '!=', 'super_admin')
            ->count();

        if ($usersWithoutOrg === 0) {
            $this->command->info('All users are already assigned to an organization.');
            return;
        }

        // Get the first admin user to make them the org owner
        $firstAdmin = User::whereNull('organization_id')
            ->where('role', 'admin')
            ->first();

        // Assign all non-super-admin users to default organization
        User::whereNull('organization_id')
            ->where('role', '!=', 'super_admin')
            ->update(['organization_id' => $organization->id]);

        // Make the first admin the organization owner
        if ($firstAdmin) {
            $firstAdmin->update(['is_organization_owner' => true]);
            $this->command->info('Made ' . $firstAdmin->email . ' the organization owner.');
        }

        $this->command->info("Assigned {$usersWithoutOrg} users to default organization.");
    }

    /**
     * Assign existing data to the default organization
     */
    protected function assignExistingDataToOrganization(Organization $organization): void
    {
        $tables = [
            'companies',
            'customers',
            'suppliers',
            'items',
            'batches',
            'sale_transactions',
            'purchase_transactions',
            'customer_receipts',
            'supplier_payments',
            'credit_notes',
            'debit_notes',
            'vouchers',
            'stock_ledgers',
        ];

        $totalUpdated = 0;

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasColumn($table, 'organization_id')) {
                $updated = DB::table($table)
                    ->whereNull('organization_id')
                    ->update(['organization_id' => $organization->id]);
                
                if ($updated > 0) {
                    $totalUpdated += $updated;
                    $this->command->line("  - {$table}: {$updated} records updated");
                }
            }
        }

        if ($totalUpdated > 0) {
            $this->command->info("Total records assigned to default organization: {$totalUpdated}");
        } else {
            $this->command->info('All data is already assigned to an organization.');
        }
    }
}
