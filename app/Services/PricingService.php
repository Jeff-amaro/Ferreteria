<?php

namespace App\Services;

use App\Models\Product;

class PricingService
{
    public const IGV_RATE = 0.18;

    public function calculate(array $lines, float $extraDiscount = 0, float $shipping = 0, bool $wholesale = false): array
    {
        $subtotal = 0.0;
        $lineDiscountTotal = 0.0;
        $calculatedLines = [];

        foreach ($lines as $line) {
            /** @var Product $product */
            $product = $line['product'];
            $quantity = max(1, (int) $line['quantity']);
            $gross = round($quantity * (float) $product->price, 2);
            $rate = $this->volumeDiscountRate($quantity, $wholesale);
            $lineDiscount = round($gross * $rate / 100, 2);

            $subtotal += $gross;
            $lineDiscountTotal += $lineDiscount;

            $calculatedLines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => $quantity,
                'unit_price' => (float) $product->price,
                'discount' => $lineDiscount,
                'subtotal' => round($gross - $lineDiscount, 2),
                'volume_rate' => $rate,
            ];
        }

        $subtotal = round($subtotal, 2);
        $extraDiscount = max(0, min($extraDiscount, $subtotal - $lineDiscountTotal));
        $discount = round($lineDiscountTotal + $extraDiscount, 2);
        $taxable = round(max(0, $subtotal - $discount), 2);
        $igv = round($taxable * self::IGV_RATE, 2);
        $total = round($taxable + $igv + $shipping, 2);

        return [
            'items' => $calculatedLines,
            'subtotal' => $subtotal,
            'line_discount' => round($lineDiscountTotal, 2),
            'extra_discount' => round($extraDiscount, 2),
            'discount' => $discount,
            'taxable' => $taxable,
            'igv' => $igv,
            'shipping' => round($shipping, 2),
            'total' => $total,
        ];
    }

    public function volumeDiscountRate(int $quantity, bool $wholesale = false): float
    {
        if (! $wholesale) {
            return 0.0;
        }

        return match (true) {
            $quantity >= 100 => 15.0,
            $quantity >= 50 => 10.0,
            $quantity >= 12 => 5.0,
            default => 0.0,
        };
    }

    public function suggestedWholesaleDiscount(float $subtotal): float
    {
        return match (true) {
            $subtotal >= 5000 => 15.0,
            $subtotal >= 2500 => 10.0,
            $subtotal >= 1000 => 5.0,
            default => 0.0,
        };
    }
}
