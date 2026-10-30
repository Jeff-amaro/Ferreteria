<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Herramientas manuales', 'description' => 'Martillos, destornilladores, llaves y accesorios de mano.'],
            ['name' => 'Herramientas eléctricas', 'description' => 'Taladros, amoladoras, sierras y equipos de potencia.'],
            ['name' => 'Plomería', 'description' => 'Tuberías, conexiones, grifería y accesorios de agua.'],
            ['name' => 'Electricidad', 'description' => 'Cables, interruptores, tomacorrientes y luminarias.'],
            ['name' => 'Pinturas', 'description' => 'Pinturas, esmaltes, brochas y accesorios de acabado.'],
            ['name' => 'Materiales de construcción', 'description' => 'Cemento, yeso, drywall y materiales básicos.'],
            ['name' => 'Tornillería', 'description' => 'Tornillos, pernos, tuercas y fijaciones.'],
            ['name' => 'Seguridad industrial', 'description' => 'EPPs, guantes, cascos y protección personal.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                    'active' => true,
                ]
            );
        }
    }
}
