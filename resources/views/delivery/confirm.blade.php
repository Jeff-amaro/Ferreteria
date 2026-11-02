@extends('layouts.app')

@section('title', 'Confirmar entrega '.$order->code)

@section('content')
    <x-page-header :title="'Confirmar entrega · '.$order->code" subtitle="Valida el código de confirmación y cierra la entrega del pedido.">
        <a href="{{ route('delivery.show', $order) }}" class="btn-ghost">Volver al detalle</a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="card p-6">
            <form method="POST" action="{{ route('delivery.confirm.store', $order) }}" class="space-y-5">
                @csrf

                @if ($order->incidencia_nota)
                    <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                        <p class="font-semibold">Incidencia registrada</p>
                        <p class="mt-1">{{ $order->incidencia_nota }}</p>
                    </div>
                @endif

                <div class="rounded-xl border border-primary-100 bg-primary-50 p-4 text-sm text-primary-800">
                    <p class="font-semibold">Confirma la entrega con el cliente</p>
                    <p class="mt-1">Solicita el código de confirmación de 6 dígitos. Al validarlo, la orden pasará a <strong>Entregado</strong> y se registrará la fecha real de entrega.</p>
                </div>

                <x-input
                    name="codigo_confirmacion"
                    label="Código de confirmación"
                    required
                    maxlength="12"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    placeholder="Ej. 482915"
                    help="Si el cliente no cuenta con el código, comunícate con el equipo de ventas antes de continuar."
                />

                <x-textarea
                    name="notes"
                    label="Nota de entrega (opcional)"
                    rows="4"
                    :value="$order->deliveryConfirmation?->notes"
                    placeholder="Observaciones finales, nombre de quien recibió, detalles del paquete..."
                />

                <div class="flex flex-wrap justify-end gap-2">
                    <a href="{{ route('delivery.show', $order) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <x-icon name="check" class="h-4 w-4" /> Confirmar entrega
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Datos de entrega</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Cliente</dt><dd class="text-right font-medium">{{ $order->client?->name }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Documento</dt><dd class="text-right">{{ $order->client?->document_number }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Teléfono</dt><dd class="text-right">{{ $order->client?->phone ?? 'No registrado' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Dirección</dt><dd class="max-w-[220px] text-right">{{ $order->delivery_address ?: 'Por confirmar' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Fecha estimada</dt><dd>{{ $order->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</dd></div>
                    <div class="flex justify-between gap-3 border-t border-slate-100 pt-2"><dt class="text-slate-500">Total</dt><dd class="font-semibold">S/ {{ number_format((float) $order->total, 2) }}</dd></div>
                </dl>
            </div>

            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Productos</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3">
                            <span class="text-slate-700">{{ $item->product_name }}</span>
                            <span class="font-semibold text-slate-900">×{{ $item->quantity }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
@endsection
