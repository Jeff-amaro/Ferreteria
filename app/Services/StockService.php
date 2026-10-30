<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function available(Product $product, int $quantity): bool
    {
        return $product->stock >= $quantity;
    }

    public function decrementForOrder(SalesOrder $order): void
    {
        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if (! $product) {
                continue;
            }

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Stock insuficiente para {$product->name}. Disponible: {$product->stock}, solicitado: {$item->quantity}.",
                ]);
            }

            $product->decrement('stock', $item->quantity);
        }
    }

    public function restoreForOrder(SalesOrder $order): void
    {
        foreach ($order->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            Product::whereKey($item->product_id)->increment('stock', $item->quantity);
        }
    }

    public function lowStock(): Collection
    {
        return Product::with('supplier')
            ->active()
            ->lowStock()
            ->orderBy('stock')
            ->get();
    }

    public function replenishmentGroups(): Collection
    {
        return $this->lowStock()
            ->groupBy(fn (Product $product) => $product->supplier?->business_name ?? 'Sin proveedor asignado')
            ->map(function (Collection $products, string $supplier) {
                return [
                    'supplier' => $supplier,
                    'products' => $products->map(function (Product $product) {
                        $product->suggested_quantity = max(1, ((int) $product->min_stock * 2) - (int) $product->stock);

                        return $product;
                    }),
                ];
            })
            ->values();
    }
}
