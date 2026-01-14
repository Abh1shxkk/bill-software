<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create organization first
        $organization = Organization::firstOrCreate(
            ['code' => 'DEMO2026'],
            [
                'name' => 'Demo Organization',
                'email' => 'demo@organization.com',
                'phone' => '1234567890',
                'address' => '123 Demo Street',
                'city' => 'Demo City',
                'state' => 'Demo State',
                'country' => 'India',
                'pin_code' => '123456',
                'status' => 'active',
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
                'date_format' => 'd/m/Y',
            ]
        );

        // Create organization admin user
        $admin = User::firstOrCreate(
            ['username' => 'orgadmin'],
            [
                'full_name' => 'Organization Admin',
                'email' => 'orgadmin@demo.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'organization_id' => $organization->id,
                'is_organization_owner' => true,
                'is_active' => true,
            ]
        );

        $this->command->info('Organization Admin Account Created:');
        $this->command->info('================================');
        $this->command->info('Username: orgadmin');
        $this->command->info('Email: orgadmin@demo.com');
        $this->command->info('Password: Admin@123');
        $this->command->info('Role: admin (Organization Admin)');
        $this->command->info('Organization: ' . $organization->name);
        $this->command->info('================================');
    }
}
