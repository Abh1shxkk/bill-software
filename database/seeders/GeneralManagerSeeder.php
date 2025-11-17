<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GeneralManager;

class GeneralManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $generalManagers = [
            [
                'name' => 'Rajesh Kumar',
                'code' => 'GM001',
                'address' => 'Corporate Headquarters, Connaught Place, New Delhi',
                'telephone' => '011-23456789',
                'mobile' => '9876543210',
                'email' => 'rajesh.kumar@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC001',
            ],
            [
                'name' => 'Priya Sharma',
                'code' => 'GM002',
                'address' => 'Regional Office, Bandra Kurla Complex, Mumbai, Maharashtra',
                'telephone' => '022-26789012',
                'mobile' => '9876543211',
                'email' => 'priya.sharma@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC002',
            ],
            [
                'name' => 'Amit Patel',
                'code' => 'GM003',
                'address' => 'Tech Park, Electronic City, Bangalore, Karnataka',
                'telephone' => '080-25678901',
                'mobile' => '9876543212',
                'email' => 'amit.patel@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC001',
            ],
            [
                'name' => 'Sunita Reddy',
                'code' => 'GM004',
                'address' => 'HITEC City, Madhapur, Hyderabad, Telangana',
                'telephone' => '040-23456789',
                'mobile' => '9876543213',
                'email' => 'sunita.reddy@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC003',
            ],
            [
                'name' => 'Vikram Singh',
                'code' => 'GM005',
                'address' => 'Business District, Prahlad Nagar, Ahmedabad, Gujarat',
                'telephone' => '079-26543210',
                'mobile' => '9876543214',
                'email' => 'vikram.singh@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC002',
            ],
            [
                'name' => 'Meera Nair',
                'code' => 'GM006',
                'address' => 'InfoPark, Kakkanad, Kochi, Kerala',
                'telephone' => '0484-2345678',
                'mobile' => '9876543215',
                'email' => 'meera.nair@company.com',
                'status' => 'Inactive',
                'dc_mgr' => 'DC004',
            ],
            [
                'name' => 'Arjun Gupta',
                'code' => 'GM007',
                'address' => 'Salt Lake City, Sector V, Kolkata, West Bengal',
                'telephone' => '033-22345678',
                'mobile' => '9876543216',
                'email' => 'arjun.gupta@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC005',
            ],
            [
                'name' => 'Kavya Iyer',
                'code' => 'GM008',
                'address' => 'OMR IT Corridor, Thoraipakkam, Chennai, Tamil Nadu',
                'telephone' => '044-24567890',
                'mobile' => '9876543217',
                'email' => 'kavya.iyer@company.com',
                'status' => 'Active',
                'dc_mgr' => 'DC003',
            ],
        ];

        foreach ($generalManagers as $generalManager) {
            GeneralManager::create($generalManager);
        }
    }
}
