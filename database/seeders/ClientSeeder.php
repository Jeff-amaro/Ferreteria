<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['DNI', '45678912', 'José Antonio Pérez', 'jose.perez@example.com', '987111222', 'Av. Arequipa 1245, Lima', 'minorista'],
            ['DNI', '41234567', 'Rosa Elena Huamán', 'rosa.huaman@example.com', '987222333', 'Jr. Puno 560, Lima', 'minorista'],
            ['RUC', '20123456789', 'Constructora San Martín SAC', 'compras@sanmartin.pe', '014445555', 'Av. Javier Prado 2100, Lima', 'mayorista'],
            ['RUC', '20456789123', 'Inversiones El Constructor EIRL', 'logistica@elconstructor.pe', '014446666', 'Av. Grau 780, Lima', 'mayorista'],
            ['DNI', '70123456', 'Miguel Ángel Flores', 'miguel.flores@example.com', '987333444', 'Calle Los Álamos 145, Lima', 'minorista'],
            ['DNI', '70987654', 'Carmen Rosa Villanueva', 'carmen.villanueva@example.com', '987444555', 'Av. Brasil 3320, Lima', 'minorista'],
            ['RUC', '20678912345', 'Servicios Generales Andinos SAC', 'abastecimiento@servandinos.pe', '014447777', 'Av. Universitaria 1850, Lima', 'mayorista'],
            ['DNI', '44556677', 'Pedro Pablo Sánchez', 'pedro.sanchez@example.com', '987555666', 'Jr. Cusco 920, Lima', 'minorista'],
            ['DNI', '46778899', 'Lucía Fernanda Ríos', 'lucia.rios@example.com', '987666777', 'Av. La Marina 2450, Lima', 'minorista'],
            ['RUC', '20987654321', 'Ferretería del Norte SAC', 'compras@ferrenorte.pe', '014448888', 'Av. Túpac Amaru 3900, Lima', 'mayorista'],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['document_number' => $client[1]],
                [
                    'document_type' => $client[0],
                    'name' => $client[2],
                    'email' => $client[3],
                    'phone' => $client[4],
                    'address' => $client[5],
                    'type' => $client[6],
                    'active' => true,
                ]
            );
        }
    }
}
