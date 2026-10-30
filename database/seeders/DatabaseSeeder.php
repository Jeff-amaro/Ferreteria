<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ClientSeeder::class,
            DemoSalesSeeder::class,
            PurchaseOrderSeeder::class,
            InventoryDemoSeeder::class,
        ]);
    }
}
