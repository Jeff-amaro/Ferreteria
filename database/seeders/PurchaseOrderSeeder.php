<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ejj.pe')->first();
        $andina = Supplier::where('ruc', '20512345678')->first();
        $bosch = Supplier::where('ruc', '20611223344')->first();
        $pavco = Supplier::where('ruc', '20699887766')->first();

        if (! $admin || ! $andina || ! $bosch || ! $pavco) {
            return;
        }

        $orders = [
            [
                'code' => 'OC-00001',
                'supplier_id' => $andina->id,
                'status' => 'en_transito',
                'ordered_at' => now()->subDays(12)->toDateString(),
                'expected_at' => now()->subDays(3)->toDateString(),
                'items' => [['HERR-003', 20, 29.50], ['SEG-002', 50, 6.50]],
                'notes' => 'Reposición de llaves ajustables y guantes de nitrilo.',
            ],
            [
                'code' => 'OC-00002',
                'supplier_id' => $bosch->id,
                'status' => 'en_transito',
                'ordered_at' => now()->subDays(2)->toDateString(),
                'expected_at' => now()->addDays(8)->toDateString(),
                'items' => [['ELEC-003', 8, 355.00], ['ELEC-001', 10, 245.00]],
                'notes' => 'Compra programada de herramientas eléctricas.',
            ],
            [
                'code' => 'OC-00003',
                'supplier_id' => $pavco->id,
                'status' => 'recibida',
                'ordered_at' => now()->subDays(20)->toDateString(),
                'expected_at' => now()->subDays(14)->toDateString(),
                'received_at' => now()->subDays(13)->toDateString(),
                'items' => [['PLOM-001', 60, 11.20], ['PLOM-002', 150, 0.60]],
                'notes' => 'Pedido de tubería y conexiones recibido completo.',
            ],
        ];

        foreach ($orders as $data) {
            $items = $data['items'];
            unset($data['items']);

            $total = 0;
            foreach ($items as [, $qty, $cost]) {
                $total += $qty * $cost;
            }

            $order = PurchaseOrder::firstOrCreate(
                ['code' => $data['code']],
                array_merge($data, [
                    'user_id' => $admin->id,
                    'total' => $total,
                ])
            );

            if ($order->items()->count() === 0) {
                foreach ($items as [$sku, $qty, $cost]) {
                    $product = Product::where('sku', $sku)->first();
                    $order->items()->create([
                        'product_id' => $product?->id,
                        'product_name' => $product?->name ?? $sku,
                        'quantity' => $qty,
                        'unit_cost' => $cost,
                        'subtotal' => $qty * $cost,
                    ]);
                }
            }
        }

        $delayed = PurchaseOrder::where('code', 'OC-00001')->first();

        if ($delayed) {
            Alert::firstOrCreate(
                ['type' => 'retraso', 'alertable_type' => PurchaseOrder::class, 'alertable_id' => $delayed->id],
                [
                    'title' => 'Pedido a proveedor retrasado',
                    'message' => "La orden {$delayed->code} del proveedor {$delayed->supplier->business_name} excede el plazo pactado.",
                    'status' => 'pendiente',
                ]
            );
        }
    }
}
