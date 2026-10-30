<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ana Torres',
                'email' => 'admin@ejj.pe',
                'role' => UserRole::Administrador,
                'dni' => '70000001',
                'phone' => '987654321',
                'address' => 'Av. Los Ferreteros 120, Lima',
            ],
            [
                'name' => 'Luis Ramírez',
                'email' => 'vendedor@ejj.pe',
                'role' => UserRole::Vendedor,
                'dni' => '70000002',
                'phone' => '987654322',
                'address' => 'Jr. Comercio 450, Lima',
            ],
            [
                'name' => 'María Quispe',
                'email' => 'almacen@ejj.pe',
                'role' => UserRole::Almacenero,
                'dni' => '70000003',
                'phone' => '987654323',
                'address' => 'Av. Almacenes 780, Lima',
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'cliente@ejj.pe',
                'role' => UserRole::Cliente,
                'dni' => '70000004',
                'phone' => '987654324',
                'address' => 'Calle Las Flores 210, Lima',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, ['password' => 'password', 'active' => true])
            );
        }
    }
}
