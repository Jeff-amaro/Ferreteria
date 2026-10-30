<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\SalesOrder;

class ReceiptService
{
    public function issue(SalesOrder $order, Payment $payment): Receipt
    {
        $type = $order->client->document_type === 'RUC' ? 'factura' : 'boleta';
        $series = $type === 'factura' ? 'F001' : 'B001';

        $lastNumber = Receipt::where('type', $type)
            ->where('series', $series)
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('number');

        $number = str_pad((string) (((int) $lastNumber) + 1), 8, '0', STR_PAD_LEFT);
        $code = $series.'-'.$number;

        return Receipt::create([
            'sales_order_id' => $order->id,
            'payment_id' => $payment->id,
            'type' => $type,
            'series' => $series,
            'number' => $number,
            'code' => $code,
            'total' => $order->total,
            'issued_at' => now(),
        ]);
    }
}
