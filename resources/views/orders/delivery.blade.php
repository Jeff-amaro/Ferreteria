@extends('layouts.app')

@section('title', 'Confirmar entrega '.$order->code)

@section('content')
    <x-page-header :title="'Confirmar entrega · '.$order->code" subtitle="Registra la recepción del pedido por parte del cliente.">
        <a href="{{ route('orders.show', $order) }}" class="btn-ghost">Volver a la orden</a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
        <div class="card p-6">
            <form method="POST" action="{{ route('orders.delivery.store', $order) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input name="received_by" label="Recibido por" required :value="$order->client?->name" />
                    <x-input name="document_number" label="Documento de identidad" required :value="$order->client?->document_number" />
                </div>
                <div>
                    <label class="label" for="signature">Firma o imagen de conformidad</label>
                    <input type="file" name="signature" id="signature" accept="image/*" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary-700">
                    <p class="mt-1 text-xs text-slate-500">Opcional. Formatos de imagen hasta 2 MB.</p>
                    @error('signature')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>
                <x-textarea name="notes" label="Observaciones" :value="$order->deliveryConfirmation?->notes" placeholder="Estado del paquete, incidencias, etc." />

                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                    Al confirmar la entrega, la orden pasará al estado <strong>Entregado</strong> y se cerrará el flujo de despacho.
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('orders.show', $order) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary"><x-icon name="check" class="h-4 w-4" /> Confirmar entrega</button>
                </div>
            </form>
        </div>

        <aside class="card p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Datos de entrega</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Cliente</dt><dd class="text-right font-medium">{{ $order->client?->name }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Dirección</dt><dd class="text-right">{{ $order->delivery_address ?? 'Sin dirección' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Fecha estimada</dt><dd>{{ $order->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Ítems</dt><dd>{{ $order->items->sum('quantity') }} unidades</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-2 font-semibold"><dt>Total</dt><dd>S/ {{ number_format((float) $order->total, 2) }}</dd></div>
            </dl>
        </aside>
    </div>
@endsection
