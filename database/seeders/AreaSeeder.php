<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $areas = [
            [
                'name' => 'Mumbai Central',
                'alter_code' => 'MBC001',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Delhi North',
                'alter_code' => 'DLN002',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Bangalore South',
                'alter_code' => 'BLS003',
                'status' => 'Inactive',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Chennai East',
                'alter_code' => 'CHE004',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Kolkata West',
                'alter_code' => 'KLW005',
                'status' => 'Pending',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Hyderabad Central',
                'alter_code' => 'HYC006',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Pune North',
                'alter_code' => 'PNN007',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Ahmedabad West',
                'alter_code' => 'AHW008',
                'status' => 'Inactive',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Jaipur South',
                'alter_code' => 'JPS009',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Lucknow East',
                'alter_code' => 'LKE010',
                'status' => 'Pending',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Surat Central',
                'alter_code' => 'SRC011',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Kanpur North',
                'alter_code' => 'KPN012',
                'status' => 'Inactive',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Nagpur West',
                'alter_code' => 'NGW013',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Indore South',
                'alter_code' => 'IDS014',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Bhopal Central',
                'alter_code' => 'BHC015',
                'status' => 'Pending',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Visakhapatnam East',
                'alter_code' => 'VSE016',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Vadodara North',
                'alter_code' => 'VDN017',
                'status' => 'Inactive',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Coimbatore West',
                'alter_code' => 'CBW018',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Agra South',
                'alter_code' => 'AGS019',
                'status' => 'Active',
                'is_deleted' => 0,
            ],
            [
                'name' => 'Madurai Central',
                'alter_code' => 'MDC020',
                'status' => 'Pending',
                'is_deleted' => 0,
            ],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
