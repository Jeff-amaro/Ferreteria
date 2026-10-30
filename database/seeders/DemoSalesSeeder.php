<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Receipt;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\WholesaleRequest;
use Illuminate\Database\Seeder;

class DemoSalesSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'vendedor@ejj.pe')->first();
        $admin = User::where('email', 'admin@ejj.pe')->first();
        $client = Client::where('document_number', '45678912')->first();
        $wholesaleClient = Client::where('document_number', '20123456789')->first();

        if (! $seller || ! $client) {
            return;
        }

        $productA = Product::where('sku', 'HERR-001')->first();
        $productB = Product::where('sku', 'ELEC-004')->first();
        $productC = Product::where('sku', 'PINT-001')->first();

        if ($productA && $productB && $productC) {
            $items = [
                [$productA, 2],
                [$productB, 1],
                [$productC, 1],
            ];

            $subtotal = 0;
            foreach ($items as [$product, $qty]) {
                $subtotal += $qty * (float) $product->price;
            }
            $shipping = 15.00;
            $discount = 0.00;
            $igv = round(($subtotal - $discount) * 0.18, 2);
            $total = round($subtotal - $discount + $shipping + $igv, 2);

            $quotation = Quotation::firstOrCreate(
                ['code' => 'COT-00001'],
                [
                    'client_id' => $client->id,
                    'user_id' => $seller->id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping' => $shipping,
                    'igv' => $igv,
                    'total' => $total,
                    'status' => 'convertida',
                    'valid_until' => now()->addDays(15),
                    'notes' => 'Cotización de demostración generada por el seeder.',
                ]
            );

            if ($quotation->items()->count() === 0) {
                foreach ($items as [$product, $qty]) {
                    $quotation->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'discount' => 0,
                        'subtotal' => $qty * (float) $product->price,
                    ]);
                }
            }

            $order = SalesOrder::firstOrCreate(
                ['code' => 'OV-00001'],
                [
                    'client_id' => $client->id,
                    'user_id' => $seller->id,
                    'quotation_id' => $quotation->id,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'shipping' => $shipping,
                    'igv' => $igv,
                    'total' => $total,
                    'status' => 'en_preparacion',
                    'payment_status' => 'pagado',
                    'delivery_address' => $client->address,
                    'delivery_date' => now()->addDays(2)->toDateString(),
                    'notes' => 'Pedido de demostración pagado en efectivo.',
                ]
            );

            if ($order->items()->count() === 0) {
                foreach ($items as [$product, $qty]) {
                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'discount' => 0,
                        'subtotal' => $qty * (float) $product->price,
                    ]);
                }

                $payment = Payment::create([
                    'sales_order_id' => $order->id,
                    'user_id' => $seller->id,
                    'method' => 'efectivo',
                    'amount' => $total + 20,
                    'change_amount' => 20,
                    'paid_at' => now()->subHours(3),
                ]);

                Receipt::create([
                    'sales_order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'type' => 'boleta',
                    'series' => 'B001',
                    'number' => '00000001',
                    'code' => 'B001-00000001',
                    'total' => $total,
                    'issued_at' => now()->subHours(3),
                ]);

                OrderStatusHistory::create([
                    'sales_order_id' => $order->id,
                    'user_id' => $seller->id,
                    'status' => 'pendiente',
                    'comment' => 'Orden registrada en el punto de venta.',
                ]);

                OrderStatusHistory::create([
                    'sales_order_id' => $order->id,
                    'user_id' => $seller->id,
                    'status' => 'en_preparacion',
                    'comment' => 'Pago confirmado, pedido en preparación.',
                ]);
            }
        }

        if ($wholesaleClient) {
            $productD = Product::where('sku', 'ELEC-001')->first();
            $productE = Product::where('sku', 'CONS-001')->first();

            if ($productD && $productE) {
                $qtyD = 2;
                $qtyE = 10;
                $subtotal = $qtyD * (float) $productD->price + $qtyE * (float) $productE->price;
                $igv = round($subtotal * 0.18, 2);

                $order = SalesOrder::firstOrCreate(
                    ['code' => 'OV-00002'],
                    [
                        'client_id' => $wholesaleClient->id,
                        'user_id' => $seller->id,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'shipping' => 0,
                        'igv' => $igv,
                        'total' => $subtotal + $igv,
                        'status' => 'pendiente',
                        'payment_status' => 'pendiente',
                        'delivery_address' => $wholesaleClient->address,
                        'delivery_date' => now()->addDays(5)->toDateString(),
                        'notes' => 'Pedido mayorista pendiente de pago.',
                    ]
                );

                if ($order->items()->count() === 0) {
                    $order->items()->createMany([
                        [
                            'product_id' => $productD->id,
                            'product_name' => $productD->name,
                            'sku' => $productD->sku,
                            'quantity' => $qtyD,
                            'unit_price' => $productD->price,
                            'discount' => 0,
                            'subtotal' => $qtyD * (float) $productD->price,
                        ],
                        [
                            'product_id' => $productE->id,
                            'product_name' => $productE->name,
                            'sku' => $productE->sku,
                            'quantity' => $qtyE,
                            'unit_price' => $productE->price,
                            'discount' => 0,
                            'subtotal' => $qtyE * (float) $productE->price,
                        ],
                    ]);

                    OrderStatusHistory::create([
                        'sales_order_id' => $order->id,
                        'user_id' => $seller->id,
                        'status' => 'pendiente',
                        'comment' => 'Orden mayorista registrada.',
                    ]);
                }
            }

            WholesaleRequest::firstOrCreate(
                ['client_id' => $wholesaleClient->id, 'status' => 'pendiente'],
                [
                    'requested_by' => $seller->id,
                    'requested_discount' => 10,
                    'notes' => 'Solicitud de descuento por volumen para próxima compra.',
                ]
            );
        }

        if ($admin) {
            $quotation = Quotation::firstOrCreate(
                ['code' => 'COT-00002'],
                [
                    'client_id' => $client->id,
                    'user_id' => $admin->id,
                    'subtotal' => 1298.00,
                    'discount' => 64.90,
                    'shipping' => 0,
                    'igv' => 221.96,
                    'total' => 1455.06,
                    'status' => 'emitida',
                    'valid_until' => now()->addDays(10),
                    'notes' => 'Cotización emitida de demostración.',
                ]
            );

            if ($quotation->items()->count() === 0) {
                $quotation->items()->create([
                    'product_id' => null,
                    'product_name' => 'Taladro percutor 650W',
                    'sku' => 'ELEC-001',
                    'quantity' => 2,
                    'unit_price' => 329.00,
                    'discount' => 0,
                    'subtotal' => 658.00,
                ]);
                $quotation->items()->create([
                    'product_id' => null,
                    'product_name' => 'Amoladora angular 4 1/2"',
                    'sku' => 'ELEC-002',
                    'quantity' => 1,
                    'unit_price' => 289.00,
                    'discount' => 0,
                    'subtotal' => 289.00,
                ]);
                $quotation->items()->create([
                    'product_id' => null,
                    'product_name' => 'Cemento Portland tipo I 42.5 kg',
                    'sku' => 'CONS-001',
                    'quantity' => 10,
                    'unit_price' => 34.90,
                    'discount' => 0,
                    'subtotal' => 349.00,
                ]);
            }
        }
    }
}
