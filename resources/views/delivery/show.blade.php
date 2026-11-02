@extends('layouts.app')

@section('title', 'Entrega '.$order->code)

@section('content')
    @php
        $canStart = in_array($order->status, ['en_preparacion', 'despachado', 'con_incidencia'], true);
        $canIncident = in_array($order->status, ['despachado', 'en_camino', 'con_incidencia'], true);
        $canConfirm = in_array($order->status, ['despachado', 'en_camino', 'con_incidencia'], true);
    @endphp

    <x-page-header :title="'Entrega '.$order->code" :subtitle="'Cliente: '.$order->client?->name.' · '.$order->delivery_address">
        <a href="{{ route('delivery.index') }}" class="btn-ghost">Volver a mis entregas</a>
        @if ($canConfirm)
            <a href="{{ route('delivery.confirm', $order) }}" class="btn-primary">
                <x-icon name="check" class="h-4 w-4" /> Registrar entrega
            </a>
        @endif
    </x-page-header>

    @if ($order->incidencia_nota)
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-semibold">Incidencia reportada</p>
            <p class="mt-1">{{ $order->incidencia_nota }}</p>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="card overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Datos de entrega</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $order->client?->name }}</p>
                        <p class="text-sm text-slate-600">{{ $order->client?->document_label }} · {{ $order->client?->type_label }}</p>
                        <p class="mt-2 flex items-start gap-2 text-sm text-slate-700">
                            <x-icon name="home" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                            <span>{{ $order->delivery_address ?: 'Dirección por confirmar' }}</span>
                        </p>
                        <a href="tel:{{ $order->client?->phone }}" class="mt-1 flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
                            <x-icon name="phone" class="h-4 w-4" />
                            {{ $order->client?->phone ?? 'Sin teléfono registrado' }}
                        </a>
                    </div>
                    <div class="sm:text-right">
                        <x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge>
                        <p class="mt-2 text-xs text-slate-500">Fecha estimada: {{ $order->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</p>
                        @if ($order->fecha_despacho)
                            <p class="text-xs text-slate-500">Despacho: {{ $order->fecha_despacho->format('d/m/Y H:i') }}</p>
                        @endif
                        @if ($order->fecha_entrega)
                            <p class="text-xs text-emerald-600">Entrega: {{ $order->fecha_entrega->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid gap-4 border-b border-slate-100 bg-slate-50/60 p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contacto</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">{{ $order->client?->phone ?? 'No registrado' }}</p>
                        <p class="text-xs text-slate-500">{{ $order->client?->email ?? 'Sin correo' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pago</p>
                        <p class="mt-1">
                            <x-status-badge :color="$order->payment_status_color">{{ $order->payment_status_label }}</x-status-badge>
                        </p>
                        <p class="mt-1 text-xs text-slate-500">Total: S/ {{ number_format((float) $order->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Confirmación</p>
                        <p class="mt-1 text-sm text-slate-700">Solicita el código al cliente al momento de entregar.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
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
                                    <td class="text-center font-semibold">{{ $item->quantity }}</td>
                                    <td class="text-right">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Historial de la entrega</h2>
                </div>
                <div class="p-5">
                    @if ($order->statusHistories->isEmpty())
                        <p class="text-sm text-slate-500">Aún no hay movimientos registrados para esta orden.</p>
                    @else
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
                    @endif
                </div>
            </div>

            @if ($order->deliveryConfirmation)
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Conformidad del cliente</h2>
                    <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-slate-500">Recibido por</dt>
                            <dd class="font-medium text-slate-800">{{ $order->deliveryConfirmation->received_by }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Documento</dt>
                            <dd class="font-medium text-slate-800">{{ $order->deliveryConfirmation->document_number ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Confirmado</dt>
                            <dd class="font-medium text-slate-800">{{ $order->deliveryConfirmation->confirmed_at?->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                    @if ($order->deliveryConfirmation->notes)
                        <p class="mt-3 rounded-xl bg-slate-50 p-3 text-sm text-slate-600">{{ $order->deliveryConfirmation->notes }}</p>
                    @endif
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Actualizar despacho</h2>
                <div class="mt-4 grid gap-2">
                    @if ($canStart)
                        <form method="POST" action="{{ route('delivery.start', $order) }}">
                            @csrf
                            <button type="submit" class="btn-primary w-full">
                                <x-icon name="truck" class="h-4 w-4" /> Marcar En camino
                            </button>
                        </form>
                    @endif

                    @if ($canIncident)
                        <a href="{{ route('delivery.incident', $order) }}" class="btn-secondary w-full text-rose-600 hover:bg-rose-50">
                            <x-icon name="alert" class="h-4 w-4" /> Reportar incidencia
                        </a>
                    @endif

                    @if ($canConfirm)
                        <a href="{{ route('delivery.confirm', $order) }}" class="btn-secondary w-full text-emerald-700 hover:bg-emerald-50">
                            <x-icon name="check" class="h-4 w-4" /> Registrar entrega
                        </a>
                    @endif

                    @if (! $canStart && ! $canIncident && ! $canConfirm)
                        <p class="text-sm text-slate-500">
                            {{ $order->status === 'entregado' ? 'La entrega de esta orden ya fue confirmada.' : 'Esta orden no tiene acciones disponibles.' }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Resumen del pedido</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Código</dt><dd class="font-semibold">{{ $order->code }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Ítems</dt><dd>{{ $order->items->sum('quantity') }} unidades</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Total</dt><dd class="font-semibold">S/ {{ number_format((float) $order->total, 2) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Pago</dt><dd>{{ $order->payment_status_label }}</dd></div>
                </dl>
            </div>

            @if ($order->notes)
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Observaciones de venta</h2>
                    <p class="mt-2 text-sm text-slate-700">{{ $order->notes }}</p>
                </div>
            @endif
        </aside>
    </div>
@endsection
