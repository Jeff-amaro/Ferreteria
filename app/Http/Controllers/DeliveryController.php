<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryConfirmationRequest;
use App\Http\Requests\DeliveryIncidentRequest;
use App\Models\DeliveryConfirmation;
use App\Models\OrderStatusHistory;
use App\Models\SalesOrder;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryController extends Controller
{
    public function __construct(private readonly SalesService $sales) {}

    public function index(Request $request)
    {
        $query = SalesOrder::with(['client', 'items'])
            ->where('repartidor_id', $request->user()->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');

                $q->where(function ($search) use ($term) {
                    $search->where('code', 'like', "%{$term}%")
                        ->orWhere('delivery_address', 'like', "%{$term}%")
                        ->orWhereHas('client', fn ($client) => $client
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('document_number', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%"));
                });
            });

        $stats = [
            'assigned' => (clone $query)->whereNotIn('status', ['entregado', 'anulado'])->count(),
            'on_route' => (clone $query)->whereIn('status', ['despachado', 'en_camino'])->count(),
            'delivered' => (clone $query)->where('status', 'entregado')->count(),
            'incidents' => (clone $query)->where('status', 'con_incidencia')->count(),
        ];

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('delivery.index', compact('orders', 'stats'));
    }

    public function show(SalesOrder $order)
    {
        $this->ensureAssigned($order);

        $order->load([
            'client',
            'items.product',
            'deliveryConfirmation',
            'statusHistories.user',
        ]);

        return view('delivery.show', compact('order'));
    }

    public function startRoute(Request $request, SalesOrder $order)
    {
        $this->ensureAssigned($order);

        if (in_array($order->status, ['entregado', 'anulado'], true)) {
            return back()->with('error', 'La orden ya se encuentra cerrada.');
        }

        if ($order->status === 'en_camino') {
            return back()->with('info', 'El pedido ya se encuentra en camino.');
        }

        if (! in_array($order->status, ['en_preparacion', 'despachado', 'con_incidencia'], true)) {
            return back()->with('error', 'La orden todavía no está lista para iniciar el despacho.');
        }

        DB::transaction(function () use ($order, $request) {
            $this->sales->updateStatus($order, 'en_camino', $request->user(), 'El repartidor inició el recorrido de entrega.');

            $order->update([
                'fecha_despacho' => $order->fecha_despacho ?? now(),
                'incidencia_nota' => null,
            ]);
        });

        return back()->with('success', 'Pedido marcado como En camino.');
    }

    public function incident(SalesOrder $order)
    {
        $this->ensureAssigned($order);

        if (in_array($order->status, ['entregado', 'anulado'], true)) {
            return redirect()->route('delivery.show', $order)
                ->with('error', 'No se puede reportar una incidencia en una orden cerrada.');
        }

        $order->load(['client', 'items']);

        return view('delivery.incident', compact('order'));
    }

    public function incidentStore(DeliveryIncidentRequest $request, SalesOrder $order)
    {
        $this->ensureAssigned($order);

        if (in_array($order->status, ['entregado', 'anulado'], true)) {
            return back()->with('error', 'No se puede reportar una incidencia en una orden cerrada.');
        }

        if (! in_array($order->status, ['despachado', 'en_camino', 'con_incidencia'], true)) {
            return back()->with('error', 'Inicia el recorrido antes de reportar una incidencia.');
        }

        $note = $request->validated('incidencia_nota');

        DB::transaction(function () use ($order, $request, $note) {
            if ($order->status !== 'con_incidencia') {
                $this->sales->updateStatus($order, 'con_incidencia', $request->user(), 'Incidencia reportada: '.$note);
            } else {
                OrderStatusHistory::create([
                    'sales_order_id' => $order->id,
                    'user_id' => $request->user()->id,
                    'status' => 'con_incidencia',
                    'comment' => 'Incidencia actualizada: '.$note,
                ]);
            }

            $order->update(['incidencia_nota' => $note]);
        });

        return redirect()->route('delivery.show', $order)
            ->with('success', 'Incidencia registrada. La orden quedó marcada con incidencia.');
    }

    public function confirm(SalesOrder $order)
    {
        $this->ensureAssigned($order);

        if ($order->status === 'entregado') {
            return redirect()->route('delivery.show', $order)
                ->with('info', 'Esta orden ya fue confirmada como entregada.');
        }

        if ($order->status === 'anulado') {
            return redirect()->route('delivery.show', $order)
                ->with('error', 'No se puede confirmar la entrega de una orden anulada.');
        }

        $order->load(['client', 'items', 'deliveryConfirmation']);

        return view('delivery.confirm', compact('order'));
    }

    public function confirmStore(DeliveryConfirmationRequest $request, SalesOrder $order)
    {
        $this->ensureAssigned($order);

        if ($order->status === 'entregado') {
            return redirect()->route('delivery.show', $order)
                ->with('info', 'Esta orden ya fue confirmada como entregada.');
        }

        if ($order->status === 'anulado') {
            return back()->with('error', 'No se puede confirmar la entrega de una orden anulada.');
        }

        if (! in_array($order->status, ['despachado', 'en_camino', 'con_incidencia'], true)) {
            return back()->with('error', 'El pedido debe estar en camino para registrar la entrega.');
        }

        $data = $request->validated();
        $code = trim($data['codigo_confirmacion']);

        if (blank($order->codigo_confirmacion) || ! hash_equals($order->codigo_confirmacion, $code)) {
            throw ValidationException::withMessages([
                'codigo_confirmacion' => 'El código de confirmación no coincide con el registrado para esta entrega.',
            ]);
        }

        DB::transaction(function () use ($order, $data, $request) {
            DeliveryConfirmation::updateOrCreate(
                ['sales_order_id' => $order->id],
                [
                    'received_by' => $order->client?->name ?? 'Cliente',
                    'document_number' => $order->client?->document_number,
                    'notes' => $data['notes'] ?? $order->incidencia_nota,
                    'confirmed_at' => now(),
                ],
            );

            $this->sales->updateStatus($order, 'entregado', $request->user(), 'Entrega confirmada con código de seguridad.');

            $order->update(['fecha_entrega' => now()]);
        });

        return redirect()->route('delivery.show', $order)
            ->with('success', 'Entrega confirmada. La orden fue marcada como Entregado.');
    }

    private function ensureAssigned(SalesOrder $order): void
    {
        if ((int) $order->repartidor_id !== (int) auth()->id()) {
            abort(403, 'Este pedido no está asignado a tu ruta.');
        }
    }
}
