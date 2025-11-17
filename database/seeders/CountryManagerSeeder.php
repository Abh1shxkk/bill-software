<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CountryManager;

class CountryManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryManagers = [
            [
                'name' => 'Sameer Verma',
                'code' => 'CM001',
                'address' => 'Corporate Tower, Connaught Place, New Delhi',
                'telephone' => '011-24567890',
                'mobile' => '9876500010',
                'email' => 'sameer.verma@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Leena Kapoor',
                'code' => 'CM002',
                'address' => 'Business Bay, BKC, Mumbai, Maharashtra',
                'telephone' => '022-26543210',
                'mobile' => '9876500011',
                'email' => 'leena.kapoor@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Arvind Rao',
                'code' => 'CM003',
                'address' => 'Tech Park, Koramangala, Bengaluru, Karnataka',
                'telephone' => '080-23456789',
                'mobile' => '9876500012',
                'email' => 'arvind.rao@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Radhika Iyer',
                'code' => 'CM004',
                'address' => 'Cyber Towers, HITEC City, Hyderabad, Telangana',
                'telephone' => '040-24567890',
                'mobile' => '9876500013',
                'email' => 'radhika.iyer@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Harshad Patel',
                'code' => 'CM005',
                'address' => 'Commerce Center, Prahlad Nagar, Ahmedabad, Gujarat',
                'telephone' => '079-22334455',
                'mobile' => '9876500014',
                'email' => 'harshad.patel@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Neha Singh',
                'code' => 'CM006',
                'address' => 'IT Corridor, Technopark, Thiruvananthapuram, Kerala',
                'telephone' => '0471-2345678',
                'mobile' => '9876500015',
                'email' => 'neha.singh@company.com',
                'status' => 'Inactive',
            ],
            [
                'name' => 'Suresh Banerjee',
                'code' => 'CM007',
                'address' => 'Salt Lake City, Sector V, Kolkata, West Bengal',
                'telephone' => '033-22112233',
                'mobile' => '9876500016',
                'email' => 'suresh.banerjee@company.com',
                'status' => 'Active',
            ],
            [
                'name' => 'Trisha Nair',
                'code' => 'CM008',
                'address' => 'OMR IT Expressway, Sholinganallur, Chennai, Tamil Nadu',
                'telephone' => '044-26667788',
                'mobile' => '9876500017',
                'email' => 'trisha.nair@company.com',
                'status' => 'Active',
            ],
        ];

        foreach ($countryManagers as $countryManager) {
            CountryManager::create($countryManager);
        }
    }
}
