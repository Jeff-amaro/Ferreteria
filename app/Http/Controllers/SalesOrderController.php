<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\PaymentRequest;
use App\Models\DeliveryConfirmation;
use App\Models\SalesOrder;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesOrderController extends Controller
{
    public function __construct(private readonly SalesService $sales) {}

    public function index(Request $request)
    {
        $orders = SalesOrder::with(['client', 'user', 'receipt'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($query) use ($term) {
                    $query->where('code', 'like', "%{$term}%")
                        ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->input('payment_status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(SalesOrder $order)
    {
        $order->load([
            'client',
            'user',
            'quotation',
            'items.product',
            'payments.user',
            'receipt',
            'deliveryConfirmation',
            'statusHistories.user',
        ]);

        return view('orders.show', compact('order'));
    }

    public function status(OrderStatusRequest $request, SalesOrder $order)
    {
        $data = $request->validated();

        $this->sales->updateStatus($order, $data['status'], $request->user(), $data['comment'] ?? null);

        return back()->with('success', 'Estado de la orden actualizado a '.$order->fresh()->status_label.'.');
    }

    public function payment(SalesOrder $order)
    {
        $order->load(['client', 'items', 'payments']);

        if ($order->payment_status === 'pagado') {
            return redirect()->route('orders.show', $order)->with('error', 'Esta orden ya se encuentra pagada.');
        }

        if ($order->status === 'anulado') {
            return redirect()->route('orders.show', $order)->with('error', 'No se puede pagar una orden anulada.');
        }

        return view('orders.payment', compact('order'));
    }

    public function paymentStore(PaymentRequest $request, SalesOrder $order)
    {
        [$payment, $receipt] = $this->sales->registerPayment($order, $request->validated(), $request->user());

        return redirect()
            ->route('receipts.show', $receipt)
            ->with('success', 'Pago registrado. Comprobante '.$receipt->code.' emitido correctamente.');
    }

    public function delivery(SalesOrder $order)
    {
        $order->load(['client', 'items', 'deliveryConfirmation']);

        if ($order->status !== 'despachado') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Solo las órdenes despachadas pueden confirmar entrega.');
        }

        return view('orders.delivery', compact('order'));
    }

    public function deliveryStore(DeliveryRequest $request, SalesOrder $order)
    {
        if ($order->status !== 'despachado') {
            throw ValidationException::withMessages([
                'status' => 'Solo las órdenes despachadas pueden confirmar entrega.',
            ]);
        }

        $data = $request->validated();
        $signaturePath = null;

        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('deliveries', 'public');
        }

        DB::transaction(function () use ($order, $data, $signaturePath, $request) {
            DeliveryConfirmation::updateOrCreate(
                ['sales_order_id' => $order->id],
                [
                    'received_by' => $data['received_by'],
                    'document_number' => $data['document_number'],
                    'signature_path' => $signaturePath ?? $order->deliveryConfirmation?->signature_path,
                    'notes' => $data['notes'] ?? null,
                    'confirmed_at' => now(),
                ],
            );

            $this->sales->updateStatus($order, 'entregado', $request->user(), 'Entrega confirmada por el cliente.');
        });

        return redirect()->route('orders.show', $order)->with('success', 'Entrega confirmada. La orden fue marcada como entregada.');
    }
}
