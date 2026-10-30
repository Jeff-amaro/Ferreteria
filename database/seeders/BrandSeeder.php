<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Truper', 'Bosch', 'Stanley', 'Makita', 'Sika', 'Pavco', '3M', 'Dewalt'] as $brand) {
            Brand::updateOrCreate(['name' => $brand], ['active' => true]);
        }
    }
}
