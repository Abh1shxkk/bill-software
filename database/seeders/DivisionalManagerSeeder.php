<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DivisionalManager;

class DivisionalManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisionalManagers = [
            [
                'name' => 'Anil Sharma',
                'code' => 'DC001',
                'address' => 'Corporate Office, Connaught Place, New Delhi',
                'telephone' => '011-23456789',
                'mobile' => '9876543220',
                'email' => 'anil.sharma@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM001',
            ],
            [
                'name' => 'Deepika Patel',
                'code' => 'DC002',
                'address' => 'Business Center, Andheri East, Mumbai, Maharashtra',
                'telephone' => '022-26789012',
                'mobile' => '9876543221',
                'email' => 'deepika.patel@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM002',
            ],
            [
                'name' => 'Ravi Kumar',
                'code' => 'DC003',
                'address' => 'Tech Hub, Koramangala, Bangalore, Karnataka',
                'telephone' => '080-25678901',
                'mobile' => '9876543222',
                'email' => 'ravi.kumar@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM001',
            ],
            [
                'name' => 'Sneha Reddy',
                'code' => 'DC004',
                'address' => 'Cyber Towers, HITEC City, Hyderabad, Telangana',
                'telephone' => '040-23456789',
                'mobile' => '9876543223',
                'email' => 'sneha.reddy@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM003',
            ],
            [
                'name' => 'Manish Gupta',
                'code' => 'DC005',
                'address' => 'Commercial Complex, Satellite, Ahmedabad, Gujarat',
                'telephone' => '079-26543210',
                'mobile' => '9876543224',
                'email' => 'manish.gupta@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM002',
            ],
            [
                'name' => 'Priyanka Nair',
                'code' => 'DC006',
                'address' => 'IT Park, Technopark, Thiruvananthapuram, Kerala',
                'telephone' => '0471-2345678',
                'mobile' => '9876543225',
                'email' => 'priyanka.nair@company.com',
                'status' => 'Inactive',
                'c_mgr' => 'CM004',
            ],
            [
                'name' => 'Sanjay Banerjee',
                'code' => 'DC007',
                'address' => 'IT Hub, Salt Lake City, Kolkata, West Bengal',
                'telephone' => '033-22345678',
                'mobile' => '9876543226',
                'email' => 'sanjay.banerjee@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM005',
            ],
            [
                'name' => 'Lakshmi Krishnan',
                'code' => 'DC008',
                'address' => 'Software Park, Sholinganallur, Chennai, Tamil Nadu',
                'telephone' => '044-24567890',
                'mobile' => '9876543227',
                'email' => 'lakshmi.krishnan@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM003',
            ],
            [
                'name' => 'Rohit Malhotra',
                'code' => 'DC009',
                'address' => 'Industrial Area, Phase 8B, Mohali, Punjab',
                'telephone' => '0172-2678901',
                'mobile' => '9876543228',
                'email' => 'rohit.malhotra@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM001',
            ],
            [
                'name' => 'Kavita Agarwal',
                'code' => 'DC010',
                'address' => 'Business District, Malviya Nagar, Jaipur, Rajasthan',
                'telephone' => '0141-2567890',
                'mobile' => '9876543229',
                'email' => 'kavita.agarwal@company.com',
                'status' => 'Active',
                'c_mgr' => 'CM002',
            ],
        ];

        foreach ($divisionalManagers as $divisionalManager) {
            DivisionalManager::create($divisionalManager);
        }
    }
}
