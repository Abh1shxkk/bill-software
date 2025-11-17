<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            [
                'name' => 'Mumbai Central Route',
                'alter_code' => 'MCR001',
                'status' => 'Active',
            ],
            [
                'name' => 'Delhi North Route',
                'alter_code' => 'DNR002',
                'status' => 'Active',
            ],
            [
                'name' => 'Bangalore South Route',
                'alter_code' => 'BSR003',
                'status' => 'Active',
            ],
            [
                'name' => 'Chennai Express Route',
                'alter_code' => 'CER004',
                'status' => 'Active',
            ],
            [
                'name' => 'Pune Metro Route',
                'alter_code' => 'PMR005',
                'status' => 'Active',
            ],
            [
                'name' => 'Hyderabad Tech Route',
                'alter_code' => 'HTR006',
                'status' => 'Active',
            ],
            [
                'name' => 'Kolkata East Route',
                'alter_code' => 'KER007',
                'status' => 'Inactive',
            ],
            [
                'name' => 'Ahmedabad West Route',
                'alter_code' => 'AWR008',
                'status' => 'Active',
            ],
            [
                'name' => 'Jaipur Heritage Route',
                'alter_code' => 'JHR009',
                'status' => 'Active',
            ],
            [
                'name' => 'Kochi Coastal Route',
                'alter_code' => 'KCR010',
                'status' => 'Active',
            ],
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }
    }
}
