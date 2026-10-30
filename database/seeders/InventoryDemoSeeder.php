<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = User::where('email', 'almacen@ejj.pe')->first();
        $admin = User::where('email', 'admin@ejj.pe')->first();

        if (! $warehouse) {
            return;
        }

        $count = InventoryCount::firstOrCreate(
            ['code' => 'INV-00001'],
            [
                'user_id' => $warehouse->id,
                'status' => 'completado',
                'notes' => 'Conteo físico de demostración en estantería principal.',
                'counted_at' => now()->subDay(),
            ]
        );

        if ($count->items()->count() === 0) {
            $samples = [
                ['HERR-001', 40],
                ['ELEC-004', 18],
                ['PINT-002', 3],
                ['PLOM-001', 120],
                ['CONS-003', 12],
            ];

            foreach ($samples as [$sku, $physical]) {
                $product = Product::where('sku', $sku)->first();
                if (! $product) {
                    continue;
                }

                $difference = $physical - $product->stock;
                $count->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'system_stock' => $product->stock,
                    'physical_stock' => $physical,
                    'difference' => $difference,
                    'balanced' => $difference === 0,
                ]);
            }
        }

        $unbalanced = $count->items()->where('balanced', false)->get();

        foreach ($unbalanced as $index => $item) {
            Audit::firstOrCreate(
                ['code' => 'AUD-'.str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT)],
                [
                    'inventory_count_id' => $count->id,
                    'reason' => "Desbalance detectado en {$item->sku}: diferencia de {$item->difference} unidades.",
                    'status' => $index === 0 ? 'pendiente' : 'en_proceso',
                    'requested_by' => $admin?->id ?? $warehouse->id,
                    'notes' => 'Auditoría generada automáticamente por el sistema.',
                ]
            );
        }
    }
}
