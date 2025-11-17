<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tablets', 'alter_code' => 'TAB001', 'status' => 'Active'],
            ['name' => 'Capsules', 'alter_code' => 'CAP002', 'status' => 'Active'],
            ['name' => 'Syrups', 'alter_code' => 'SYR003', 'status' => 'Active'],
            ['name' => 'Injections', 'alter_code' => 'INJ004', 'status' => 'Active'],
            ['name' => 'Ointments', 'alter_code' => 'OIN005', 'status' => 'Active'],
            ['name' => 'Drops', 'alter_code' => 'DRP006', 'status' => 'Active'],
            ['name' => 'Powders', 'alter_code' => 'POW007', 'status' => 'Active'],
            ['name' => 'Creams', 'alter_code' => 'CRM008', 'status' => 'Active'],
            ['name' => 'Gels', 'alter_code' => 'GEL009', 'status' => 'Active'],
            ['name' => 'Lotions', 'alter_code' => 'LOT010', 'status' => 'Active'],
            
            ['name' => 'Sprays', 'alter_code' => 'SPR011', 'status' => 'Active'],
            ['name' => 'Inhalers', 'alter_code' => 'INH012', 'status' => 'Active'],
            ['name' => 'Sachets', 'alter_code' => 'SAC013', 'status' => 'Active'],
            ['name' => 'Suspensions', 'alter_code' => 'SUS014', 'status' => 'Active'],
            ['name' => 'Solutions', 'alter_code' => 'SOL015', 'status' => 'Active'],
            ['name' => 'Emulsions', 'alter_code' => 'EMU016', 'status' => 'Active'],
            ['name' => 'Granules', 'alter_code' => 'GRA017', 'status' => 'Active'],
            ['name' => 'Patches', 'alter_code' => 'PAT018', 'status' => 'Active'],
            ['name' => 'Suppositories', 'alter_code' => 'SUP019', 'status' => 'Active'],
            ['name' => 'Pessaries', 'alter_code' => 'PES020', 'status' => 'Inactive'],
            
            ['name' => 'Liniments', 'alter_code' => 'LIN021', 'status' => 'Active'],
            ['name' => 'Tinctures', 'alter_code' => 'TIN022', 'status' => 'Active'],
            ['name' => 'Elixirs', 'alter_code' => 'ELI023', 'status' => 'Active'],
            ['name' => 'Mixtures', 'alter_code' => 'MIX024', 'status' => 'Active'],
            ['name' => 'Pastes', 'alter_code' => 'PAS025', 'status' => 'Active'],
            ['name' => 'Balms', 'alter_code' => 'BAL026', 'status' => 'Active'],
            ['name' => 'Oils', 'alter_code' => 'OIL027', 'status' => 'Active'],
            ['name' => 'Serums', 'alter_code' => 'SER028', 'status' => 'Active'],
            ['name' => 'Tonics', 'alter_code' => 'TON029', 'status' => 'Active'],
            ['name' => 'Vaccines', 'alter_code' => 'VAC030', 'status' => 'Active'],
        ];

        foreach ($categories as $category) {
            ItemCategory::create($category);
        }
    }
}
