<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            [
                'name' => 'Maharashtra',
                'alter_code' => 'MH001',
                'status' => 'Active',
            ],
            [
                'name' => 'Delhi',
                'alter_code' => 'DL002',
                'status' => 'Active',
            ],
            [
                'name' => 'Karnataka',
                'alter_code' => 'KA003',
                'status' => 'Active',
            ],
            [
                'name' => 'Tamil Nadu',
                'alter_code' => 'TN004',
                'status' => 'Active',
            ],
            [
                'name' => 'Gujarat',
                'alter_code' => 'GJ005',
                'status' => 'Active',
            ],
            [
                'name' => 'Rajasthan',
                'alter_code' => 'RJ006',
                'status' => 'Active',
            ],
            [
                'name' => 'West Bengal',
                'alter_code' => 'WB007',
                'status' => 'Inactive',
            ],
            [
                'name' => 'Andhra Pradesh',
                'alter_code' => 'AP008',
                'status' => 'Active',
            ],
            [
                'name' => 'Telangana',
                'alter_code' => 'TS009',
                'status' => 'Active',
            ],
            [
                'name' => 'Kerala',
                'alter_code' => 'KL010',
                'status' => 'Active',
            ],
            [
                'name' => 'Punjab',
                'alter_code' => 'PB011',
                'status' => 'Active',
            ],
            [
                'name' => 'Haryana',
                'alter_code' => 'HR012',
                'status' => 'Active',
            ],
        ];

        foreach ($states as $state) {
            State::create($state);
        }
    }
}
