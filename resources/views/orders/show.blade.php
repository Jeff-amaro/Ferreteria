@extends('layouts.app')

@section('title', 'Orden '.$order->code)

@section('content')
    <x-page-header :title="'Orden de venta '.$order->code" :subtitle="'Cliente: '.$order->client?->name.' · Registrada el '.$order->created_at->format('d/m/Y H:i')">
        <a href="{{ route('orders.index') }}" class="btn-ghost">Volver</a>
        @if ($order->receipt)
            <a href="{{ route('receipts.show', $order->receipt) }}" class="btn-secondary"><x-icon name="receipt" class="h-4 w-4" /> Comprobante</a>
        @elseif ($order->payment_status !== 'pagado' && $order->status !== 'anulado')
            <a href="{{ route('orders.payment', $order) }}" class="btn-primary"><x-icon name="wallet" class="h-4 w-4" /> Registrar pago</a>
        @endif
        @if ($order->status === 'despachado')
            <a href="{{ route('orders.delivery', $order) }}" class="btn-primary"><x-icon name="truck" class="h-4 w-4" /> Confirmar entrega</a>
        @endif
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="card overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cliente</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $order->client?->name }}</p>
                        <p class="text-sm text-slate-600">{{ $order->client?->document_label }} · {{ $order->client?->type_label }}</p>
                        <p class="text-sm text-slate-600">{{ $order->client?->phone ?? 'Sin teléfono' }} · {{ $order->client?->email ?? 'Sin correo' }}</p>
                        <p class="text-sm text-slate-600">Entrega: {{ $order->delivery_address ?? 'Sin dirección' }}</p>
                        <p class="text-sm text-slate-600">Fecha estimada: {{ $order->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</p>
                    </div>
                    <div class="sm:text-right">
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge>
                            <x-status-badge :color="$order->payment_status_color">{{ $order->payment_status_label }}</x-status-badge>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Vendedor: {{ $order->user?->name }}</p>
                        @if ($order->quotation)
                            <p class="text-xs text-slate-500">Origen: <a href="{{ route('quotations.show', $order->quotation) }}" class="font-semibold text-primary-600 hover:underline">{{ $order->quotation->code }}</a></p>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right">Descuento</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <p class="font-medium text-slate-800">{{ $item->product_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->sku }}</p>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-right text-emerald-600">- S/ {{ number_format((float) $item->discount, 2) }}</td>
                                    <td class="text-right font-semibold">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t border-slate-100 p-5">
                    <dl class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600"><dt>Subtotal</dt><dd>S/ {{ number_format((float) $order->subtotal, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>Descuento</dt><dd class="text-emerald-600">- S/ {{ number_format((float) $order->discount, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>Envío</dt><dd>S/ {{ number_format((float) $order->shipping, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>IGV (18%)</dt><dd>S/ {{ number_format((float) $order->igv, 2) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>S/ {{ number_format((float) $order->total, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>Pagado</dt><dd>S/ {{ number_format((float) $order->payments->sum('amount'), 2) }}</dd></div>
                        <div class="flex justify-between font-semibold text-rose-600"><dt>Saldo</dt><dd>S/ {{ number_format((float) $order->balance, 2) }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Historial de estados</h2>
                </div>
                <div class="p-5">
                    <ol class="relative space-y-4 border-l border-slate-200 pl-5">
                        @foreach ($order->statusHistories as $history)
                            <li class="relative">
                                <span class="absolute -left-[26px] top-1 h-2.5 w-2.5 rounded-full bg-primary-600 ring-4 ring-primary-100"></span>
                                <p class="text-sm font-semibold text-slate-800">{{ \App\Models\SalesOrder::STATUSES[$history->status] ?? ucfirst($history->status) }}</p>
                                <p class="text-xs text-slate-500">{{ $history->created_at->format('d/m/Y H:i') }} · {{ $history->user?->name }}</p>
                                @if ($history->comment)
                                    <p class="mt-1 text-sm text-slate-600">{{ $history->comment }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            @if ($order->payments->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="border-b border-slate-100 p-5">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Pagos registrados</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Método</th>
                                    <th>Referencia</th>
                                    <th>Registrado por</th>
                                    <th class="text-right">Monto</th>
                                    <th class="text-right">Vuelto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->payments as $payment)
                                    <tr>
                                        <td class="text-sm text-slate-600">{{ $payment->paid_at?->format('d/m/Y H:i') }}</td>
                                        <td><x-status-badge color="bg-sky-100 text-sky-700">{{ $payment->method_label }}</x-status-badge></td>
                                        <td class="text-sm text-slate-600">{{ $payment->reference ?? '—' }}</td>
                                        <td class="text-sm text-slate-600">{{ $payment->user?->name }}</td>
                                        <td class="text-right font-semibold">S/ {{ number_format((float) $payment->amount, 2) }}</td>
                                        <td class="text-right text-slate-600">S/ {{ number_format((float) $payment->change_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Actualizar estado</h2>
                @if ($order->next_statuses === [])
                    <p class="mt-2 text-sm text-slate-500">La orden se encuentra en un estado final.</p>
                @else
                    <div class="mt-3 grid gap-2">
                        @foreach ($order->next_statuses as $status)
                            <form method="POST" action="{{ route('orders.status', $order) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="hidden" name="comment" value="Actualización desde el panel de la orden.">
                                <button type="submit" class="btn-secondary w-full {{ $status === 'anulado' ? 'text-rose-600 hover:bg-rose-50' : '' }}">
                                    {{ \App\Models\SalesOrder::STATUSES[$status] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($order->deliveryConfirmation)
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Entrega confirmada</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Recibido por</dt><dd class="text-right font-medium">{{ $order->deliveryConfirmation->received_by }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Documento</dt><dd class="text-right">{{ $order->deliveryConfirmation->document_number }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Fecha</dt><dd class="text-right">{{ $order->deliveryConfirmation->confirmed_at?->format('d/m/Y H:i') }}</dd></div>
                    </dl>
                    @if ($order->deliveryConfirmation->signature_path)
                        <a href="{{ asset('storage/'.$order->deliveryConfirmation->signature_path) }}" target="_blank" class="btn-secondary mt-3 w-full">Ver firma</a>
                    @endif
                </div>
            @endif

            @if ($order->notes)
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Observaciones</h2>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->notes }}</p>
                </div>
            @endif
        </aside>
    </div>
@endsection
