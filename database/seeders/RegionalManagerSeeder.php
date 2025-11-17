<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegionalManager;

class RegionalManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionalManagers = [
            [
                'name' => 'Suresh Kumar Agarwal',
                'code' => 'RM001',
                'address' => 'Corporate Office, Connaught Place, New Delhi',
                'telephone' => '011-23456789',
                'mobile' => '9876543220',
                'email' => 'suresh.agarwal@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM001',
            ],
            [
                'name' => 'Deepika Sharma',
                'code' => 'RM002',
                'address' => 'Regional Office, Bandra Kurla Complex, Mumbai, Maharashtra',
                'telephone' => '022-26789012',
                'mobile' => '9876543221',
                'email' => 'deepika.sharma@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM002',
            ],
            [
                'name' => 'Ravi Chandran',
                'code' => 'RM003',
                'address' => 'Tech Park, Electronic City, Bangalore, Karnataka',
                'telephone' => '080-25678901',
                'mobile' => '9876543222',
                'email' => 'ravi.chandran@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM001',
            ],
            [
                'name' => 'Anjali Verma',
                'code' => 'RM004',
                'address' => 'Business District, HITEC City, Hyderabad, Telangana',
                'telephone' => '040-23456789',
                'mobile' => '9876543223',
                'email' => 'anjali.verma@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM003',
            ],
            [
                'name' => 'Manoj Singh Rajput',
                'code' => 'RM005',
                'address' => 'Commercial Complex, MI Road, Jaipur, Rajasthan',
                'telephone' => '0141-2567890',
                'mobile' => '9876543224',
                'email' => 'manoj.rajput@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM002',
            ],
            [
                'name' => 'Priyanka Nair',
                'code' => 'RM006',
                'address' => 'Marine Drive Business Center, Kochi, Kerala',
                'telephone' => '0484-2345678',
                'mobile' => '9876543225',
                'email' => 'priyanka.nair@company.com',
                'status' => 'Inactive',
                'mkt_mgr' => 'MM004',
            ],
            [
                'name' => 'Abhishek Ghosh',
                'code' => 'RM007',
                'address' => 'Salt Lake Sector V, Kolkata, West Bengal',
                'telephone' => '033-22345678',
                'mobile' => '9876543226',
                'email' => 'abhishek.ghosh@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM005',
            ],
            [
                'name' => 'Kavitha Reddy',
                'code' => 'RM008',
                'address' => 'Anna Salai Business District, Chennai, Tamil Nadu',
                'telephone' => '044-24567890',
                'mobile' => '9876543227',
                'email' => 'kavitha.reddy@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM003',
            ],
            [
                'name' => 'Harpreet Singh',
                'code' => 'RM009',
                'address' => 'Industrial Area Phase 1, Chandigarh',
                'telephone' => '0172-2678901',
                'mobile' => '9876543228',
                'email' => 'harpreet.singh@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM001',
            ],
            [
                'name' => 'Neha Joshi',
                'code' => 'RM010',
                'address' => 'Satellite Business Park, Ahmedabad, Gujarat',
                'telephone' => '079-26543210',
                'mobile' => '9876543229',
                'email' => 'neha.joshi@company.com',
                'status' => 'Active',
                'mkt_mgr' => 'MM002',
            ],
        ];

        foreach ($regionalManagers as $regionalManager) {
            RegionalManager::create($regionalManager);
        }
    }
}
