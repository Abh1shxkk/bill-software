<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalesMan;

class SalesManSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesMenData = [
            [
                'code' => 'SM001',
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.kumar@company.com',
                'mobile' => '9876543210',
                'telephone' => '022-12345678',
                'address' => '123 MG Road, Andheri East',
                'city' => 'Mumbai',
                'pin' => '400069',
                'sales_type' => 'S',
                'delivery_type' => 'S',
                'designation' => 'Senior Sales Executive',
                'area_mgr_code' => '01',
                'area_mgr_name' => 'MUMBAI WEST',
                'target_amount' => 50000.00,
                'monthly_target' => 25000.00,
                'commission_percent' => 2.5,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM002',
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@company.com',
                'mobile' => '9876543211',
                'telephone' => '011-87654321',
                'address' => '456 CP Market, Connaught Place',
                'city' => 'Delhi',
                'pin' => '110001',
                'sales_type' => 'C',
                'delivery_type' => 'D',
                'designation' => 'Collection Executive',
                'area_mgr_code' => '02',
                'area_mgr_name' => 'DELHI CENTRAL',
                'target_amount' => 40000.00,
                'monthly_target' => 20000.00,
                'commission_percent' => 2.0,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM003',
                'name' => 'Amit Patel',
                'email' => 'amit.patel@company.com',
                'mobile' => '9876543212',
                'telephone' => '079-11223344',
                'address' => '789 SG Highway, Satellite',
                'city' => 'Ahmedabad',
                'pin' => '380015',
                'sales_type' => 'B',
                'delivery_type' => 'B',
                'designation' => 'Sales Manager',
                'area_mgr_code' => '03',
                'area_mgr_name' => 'GUJARAT NORTH',
                'target_amount' => 75000.00,
                'monthly_target' => 35000.00,
                'commission_percent' => 3.0,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM004',
                'name' => 'Sneha Reddy',
                'email' => 'sneha.reddy@company.com',
                'mobile' => '9876543213',
                'telephone' => '040-55667788',
                'address' => '321 Banjara Hills, Road No 12',
                'city' => 'Hyderabad',
                'pin' => '500034',
                'sales_type' => 'S',
                'delivery_type' => 'S',
                'designation' => 'Territory Sales Officer',
                'area_mgr_code' => '04',
                'area_mgr_name' => 'TELANGANA SOUTH',
                'target_amount' => 45000.00,
                'monthly_target' => 22000.00,
                'commission_percent' => 2.2,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM005',
                'name' => 'Vikram Singh',
                'email' => 'vikram.singh@company.com',
                'mobile' => '9876543214',
                'telephone' => '0141-99887766',
                'address' => '654 MI Road, C-Scheme',
                'city' => 'Jaipur',
                'pin' => '302001',
                'sales_type' => 'S',
                'delivery_type' => 'D',
                'designation' => 'Field Sales Representative',
                'area_mgr_code' => '05',
                'area_mgr_name' => 'RAJASTHAN EAST',
                'target_amount' => 35000.00,
                'monthly_target' => 18000.00,
                'commission_percent' => 1.8,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM006',
                'name' => 'Kavya Nair',
                'email' => 'kavya.nair@company.com',
                'mobile' => '9876543215',
                'telephone' => '0484-44556677',
                'address' => '987 MG Road, Ernakulam',
                'city' => 'Kochi',
                'pin' => '682016',
                'sales_type' => 'C',
                'delivery_type' => 'S',
                'designation' => 'Sales Associate',
                'area_mgr_code' => '06',
                'area_mgr_name' => 'KERALA CENTRAL',
                'target_amount' => 30000.00,
                'monthly_target' => 15000.00,
                'commission_percent' => 1.5,
                'status' => 0,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM007',
                'name' => 'Arjun Gupta',
                'email' => 'arjun.gupta@company.com',
                'mobile' => '9876543216',
                'telephone' => '033-22334455',
                'address' => '111 Park Street, Kolkata',
                'city' => 'Kolkata',
                'pin' => '700016',
                'sales_type' => 'S',
                'delivery_type' => 'S',
                'designation' => 'Regional Sales Head',
                'area_mgr_code' => '07',
                'area_mgr_name' => 'WEST BENGAL',
                'target_amount' => 80000.00,
                'monthly_target' => 40000.00,
                'commission_percent' => 3.5,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ],
            [
                'code' => 'SM008',
                'name' => 'Meera Iyer',
                'email' => 'meera.iyer@company.com',
                'mobile' => '9876543217',
                'telephone' => '044-66778899',
                'address' => '222 Anna Salai, T Nagar',
                'city' => 'Chennai',
                'pin' => '600017',
                'sales_type' => 'B',
                'delivery_type' => 'B',
                'designation' => 'Area Sales Manager',
                'area_mgr_code' => '08',
                'area_mgr_name' => 'TAMIL NADU',
                'target_amount' => 60000.00,
                'monthly_target' => 30000.00,
                'commission_percent' => 2.8,
                'status' => 1,
                'created_date' => now(),
                'modified_date' => now()
            ]
        ];

        foreach ($salesMenData as $data) {
            SalesMan::create($data);
        }
    }
}
