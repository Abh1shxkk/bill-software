<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingManager;

class MarketingManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marketingManagers = [
            [
                'name' => 'Arjun Kapoor',
                'code' => 'MM001',
                'address' => 'Head Office, Nariman Point, Mumbai, Maharashtra',
                'telephone' => '022-26789012',
                'mobile' => '9876543230',
                'email' => 'arjun.kapoor@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM001',
            ],
            [
                'name' => 'Sanya Malhotra',
                'code' => 'MM002',
                'address' => 'Corporate Tower, Cyber City, Gurgaon, Haryana',
                'telephone' => '0124-2345678',
                'mobile' => '9876543231',
                'email' => 'sanya.malhotra@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM002',
            ],
            [
                'name' => 'Vikash Sinha',
                'code' => 'MM003',
                'address' => 'IT Hub, Whitefield, Bangalore, Karnataka',
                'telephone' => '080-25678901',
                'mobile' => '9876543232',
                'email' => 'vikash.sinha@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM001',
            ],
            [
                'name' => 'Ritika Sharma',
                'code' => 'MM004',
                'address' => 'Financial District, Gachibowli, Hyderabad, Telangana',
                'telephone' => '040-23456789',
                'mobile' => '9876543233',
                'email' => 'ritika.sharma@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM003',
            ],
            [
                'name' => 'Karan Johar',
                'code' => 'MM005',
                'address' => 'Business Park, Vastrapur, Ahmedabad, Gujarat',
                'telephone' => '079-26543210',
                'mobile' => '9876543234',
                'email' => 'karan.johar@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM002',
            ],
            [
                'name' => 'Pooja Hegde',
                'code' => 'MM006',
                'address' => 'IT Corridor, Technopark, Thiruvananthapuram, Kerala',
                'telephone' => '0471-2345678',
                'mobile' => '9876543235',
                'email' => 'pooja.hegde@company.com',
                'status' => 'Inactive',
                'gen_mgr' => 'GM004',
            ],
            [
                'name' => 'Rohit Sharma',
                'code' => 'MM007',
                'address' => 'New Town, Action Area II, Kolkata, West Bengal',
                'telephone' => '033-22345678',
                'mobile' => '9876543236',
                'email' => 'rohit.sharma@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM005',
            ],
            [
                'name' => 'Shraddha Kapoor',
                'code' => 'MM008',
                'address' => 'IT Expressway, Sholinganallur, Chennai, Tamil Nadu',
                'telephone' => '044-24567890',
                'mobile' => '9876543237',
                'email' => 'shraddha.kapoor@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM003',
            ],
            [
                'name' => 'Ayushmann Khurrana',
                'code' => 'MM009',
                'address' => 'IT City, Mohali, Punjab',
                'telephone' => '0172-2678901',
                'mobile' => '9876543238',
                'email' => 'ayushmann.khurrana@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM001',
            ],
            [
                'name' => 'Kiara Advani',
                'code' => 'MM010',
                'address' => 'Pink City Junction, Jaipur, Rajasthan',
                'telephone' => '0141-2567890',
                'mobile' => '9876543239',
                'email' => 'kiara.advani@company.com',
                'status' => 'Active',
                'gen_mgr' => 'GM002',
            ],
        ];

        foreach ($marketingManagers as $marketingManager) {
            MarketingManager::create($marketingManager);
        }
    }
}
