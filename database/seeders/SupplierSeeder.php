<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'ruc' => '20512345678',
                'business_name' => 'Distribuidora Ferretera Andina SAC',
                'contact_name' => 'Jorge Salazar',
                'phone' => '014567890',
                'email' => 'ventas@andinaferretera.pe',
                'address' => 'Av. Argentina 2450, Lima',
                'lead_time_days' => 5,
            ],
            [
                'ruc' => '20587654321',
                'business_name' => 'Importaciones Truper Perú SAC',
                'contact_name' => 'Rosa Delgado',
                'phone' => '014567891',
                'email' => 'pedidos@truperperu.pe',
                'address' => 'Av. Colonial 890, Lima',
                'lead_time_days' => 7,
            ],
            [
                'ruc' => '20611223344',
                'business_name' => 'Bosch Power Tools Perú SAC',
                'contact_name' => 'Diego Paredes',
                'phone' => '014567892',
                'email' => 'contacto@boschperu.pe',
                'address' => 'Av. Javier Prado 1290, Lima',
                'lead_time_days' => 10,
            ],
            [
                'ruc' => '20699887766',
                'business_name' => 'Pavco Comercial del Perú SAC',
                'contact_name' => 'Silvia Rojas',
                'phone' => '014567893',
                'email' => 'ventas@pavco.pe',
                'address' => 'Av. Industrial 3400, Lima',
                'lead_time_days' => 4,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['ruc' => $supplier['ruc']], array_merge($supplier, ['active' => true]));
        }
    }
}
