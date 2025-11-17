<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AreaManager;

class AreaManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areaManagers = [
            [
                'name' => 'Rajesh Kumar Sharma',
                'code' => 'AM001',
                'address' => 'Plot No. 45, Sector 12, Noida, Uttar Pradesh',
                'telephone' => '0120-2345678',
                'mobile' => '9876543210',
                'email' => 'rajesh.sharma@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM001',
            ],
            [
                'name' => 'Priya Patel',
                'code' => 'AM002',
                'address' => 'B-204, Satellite Road, Ahmedabad, Gujarat',
                'telephone' => '079-26543210',
                'mobile' => '9876543211',
                'email' => 'priya.patel@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM002',
            ],
            [
                'name' => 'Amit Singh',
                'code' => 'AM003',
                'address' => 'Flat 301, Koramangala, Bangalore, Karnataka',
                'telephone' => '080-25678901',
                'mobile' => '9876543212',
                'email' => 'amit.singh@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM001',
            ],
            [
                'name' => 'Sneha Reddy',
                'code' => 'AM004',
                'address' => 'House No. 12-3-456, Banjara Hills, Hyderabad, Telangana',
                'telephone' => '040-23456789',
                'mobile' => '9876543213',
                'email' => 'sneha.reddy@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM003',
            ],
            [
                'name' => 'Vikram Agarwal',
                'code' => 'AM005',
                'address' => 'C-15, Civil Lines, Jaipur, Rajasthan',
                'telephone' => '0141-2567890',
                'mobile' => '9876543214',
                'email' => 'vikram.agarwal@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM002',
            ],
            [
                'name' => 'Kavya Nair',
                'code' => 'AM006',
                'address' => 'TC 25/1234, Pattom, Thiruvananthapuram, Kerala',
                'telephone' => '0471-2345678',
                'mobile' => '9876543215',
                'email' => 'kavya.nair@company.com',
                'status' => 'Inactive',
                'reg_mgr' => 'RM004',
            ],
            [
                'name' => 'Arjun Gupta',
                'code' => 'AM007',
                'address' => '45/2, Park Street, Kolkata, West Bengal',
                'telephone' => '033-22345678',
                'mobile' => '9876543216',
                'email' => 'arjun.gupta@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM005',
            ],
            [
                'name' => 'Meera Iyer',
                'code' => 'AM008',
                'address' => 'No. 23, T. Nagar, Chennai, Tamil Nadu',
                'telephone' => '044-24567890',
                'mobile' => '9876543217',
                'email' => 'meera.iyer@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM003',
            ],
            [
                'name' => 'Rohit Malhotra',
                'code' => 'AM009',
                'address' => 'SCO 234, Sector 35, Chandigarh',
                'telephone' => '0172-2678901',
                'mobile' => '9876543218',
                'email' => 'rohit.malhotra@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM001',
            ],
            [
                'name' => 'Anita Desai',
                'code' => 'AM010',
                'address' => 'Flat 502, Bandra West, Mumbai, Maharashtra',
                'telephone' => '022-26789012',
                'mobile' => '9876543219',
                'email' => 'anita.desai@company.com',
                'status' => 'Active',
                'reg_mgr' => 'RM002',
            ],
        ];

        foreach ($areaManagers as $areaManager) {
            AreaManager::create($areaManager);
        }
    }
}
