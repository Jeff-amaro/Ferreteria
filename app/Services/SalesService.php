<?php

namespace App\Services;

use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesService
{
    public function __construct(
        private readonly CodeGeneratorService $codes,
        private readonly StockService $stock,
        private readonly ReceiptService $receipts,
    ) {}

    public function createFromQuotation(Quotation $quotation, User $user): SalesOrder
    {
        if ($quotation->salesOrder()->exists()) {
            throw ValidationException::withMessages([
                'quotation' => 'Esta cotización ya fue convertida en una orden de venta.',
            ]);
        }

        return DB::transaction(function () use ($quotation, $user) {
            $order = SalesOrder::create([
                'code' => $this->codes->next('OV', SalesOrder::class),
                'client_id' => $quotation->client_id,
                'user_id' => $user->id,
                'quotation_id' => $quotation->id,
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'shipping' => $quotation->shipping,
                'igv' => $quotation->igv,
                'total' => $quotation->total,
                'status' => 'pendiente',
                'payment_status' => 'pendiente',
                'delivery_address' => $quotation->client->address,
                'delivery_date' => now()->addDays(3)->toDateString(),
                'notes' => $quotation->notes,
            ]);

            foreach ($quotation->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $this->stock->decrementForOrder($order);

            $quotation->update(['status' => 'convertida']);

            $this->history($order, 'pendiente', 'Orden de venta generada desde la cotización '.$quotation->code.'.', $user);

            return $order->fresh(['items', 'client']);
        });
    }

    public function updateStatus(SalesOrder $order, string $status, User $user, ?string $comment = null): SalesOrder
    {
        if (! $order->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => 'La transición de estado solicitada no es válida.',
            ]);
        }

        return DB::transaction(function () use ($order, $status, $user, $comment) {
            if ($status === 'anulado') {
                $this->stock->restoreForOrder($order);
            }

            $order->update(['status' => $status]);
            $this->history($order, $status, $comment, $user);

            return $order->fresh();
        });
    }

    public function registerPayment(SalesOrder $order, array $data, User $user): array
    {
        if ($order->payment_status === 'pagado') {
            throw ValidationException::withMessages([
                'payment' => 'Esta orden ya se encuentra pagada.',
            ]);
        }

        if ($order->status === 'anulado') {
            throw ValidationException::withMessages([
                'payment' => 'No se puede registrar un pago sobre una orden anulada.',
            ]);
        }

        $amount = round((float) $data['amount'], 2);
        $balance = round((float) $order->balance, 2);

        if ($amount < $balance) {
            throw ValidationException::withMessages([
                'amount' => 'El monto recibido es menor al saldo pendiente de S/ '.number_format($balance, 2).'.',
            ]);
        }

        if ($data['method'] !== 'efectivo' && $amount !== $balance) {
            throw ValidationException::withMessages([
                'amount' => 'Para pagos electrónicos el monto debe coincidir exactamente con el total.',
            ]);
        }

        return DB::transaction(function () use ($order, $data, $user, $amount, $balance) {
            $change = $data['method'] === 'efectivo' ? round($amount - $balance, 2) : 0.0;

            $payment = Payment::create([
                'sales_order_id' => $order->id,
                'user_id' => $user->id,
                'method' => $data['method'],
                'amount' => $amount,
                'change_amount' => $change,
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
            ]);

            $order->update(['payment_status' => 'pagado']);

            $receipt = $this->receipts->issue($order, $payment);

            if ($order->status === 'pendiente') {
                $order->update(['status' => 'en_preparacion']);
                $this->history($order, 'en_preparacion', 'Pago registrado. Pedido en preparación.', $user);
            }

            return [$payment, $receipt];
        });
    }

    private function history(SalesOrder $order, string $status, ?string $comment, User $user): void
    {
        OrderStatusHistory::create([
            'sales_order_id' => $order->id,
            'user_id' => $user->id,
            'status' => $status,
            'comment' => $comment,
        ]);
    }
}
