@extends('layouts.app')

@section('title', 'Reportar incidencia '.$order->code)

@section('content')
    <x-page-header :title="'Reportar incidencia · '.$order->code" subtitle="Registra el problema presentado durante la entrega para dar seguimiento.">
        <a href="{{ route('delivery.show', $order) }}" class="btn-ghost">Volver al detalle</a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="card p-6">
            <form method="POST" action="{{ route('delivery.incident.store', $order) }}" class="space-y-5">
                @csrf

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    La orden quedará en estado <strong>Con incidencia</strong>. Podrás retomar la ruta o registrar la entrega cuando el problema se resuelva.
                </div>

                <x-textarea
                    name="incidencia_nota"
                    label="Detalle de la incidencia"
                    rows="6"
                    required
                    :value="$order->incidencia_nota"
                    placeholder="Ej. El cliente no se encontraba en el domicilio, dirección incorrecta, producto dañado, acceso bloqueado..."
                    help="Describe el problema con claridad para que el equipo pueda coordinarlo con el cliente."
                />

                <div class="flex flex-wrap justify-end gap-2">
                    <a href="{{ route('delivery.show', $order) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-danger">
                        <x-icon name="alert" class="h-4 w-4" /> Registrar incidencia
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Datos de la entrega</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Cliente</dt><dd class="text-right font-medium">{{ $order->client?->name }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Teléfono</dt><dd class="text-right">{{ $order->client?->phone ?? 'No registrado' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Dirección</dt><dd class="max-w-[220px] text-right">{{ $order->delivery_address ?: 'Por confirmar' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Estado</dt><dd><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></dd></div>
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
