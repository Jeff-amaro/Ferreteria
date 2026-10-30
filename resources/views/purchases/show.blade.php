@extends('layouts.app')

@section('title', 'Compra '.$purchaseOrder->code)

@section('content')
    <x-page-header :title="'Orden de compra '.$purchaseOrder->code" :subtitle="'Proveedor: '.$purchaseOrder->supplier?->business_name.' · Registrada el '.$purchaseOrder->created_at->format('d/m/Y H:i')">
        <a href="{{ route('purchases.index') }}" class="btn-ghost">Volver</a>
    </x-page-header>

    @if ($purchaseOrder->is_delayed)
        <div class="mb-5"><x-alert type="warning">Esta orden presenta un retraso de {{ $purchaseOrder->days_late }} días respecto a la fecha esperada ({{ $purchaseOrder->expected_at?->format('d/m/Y') }}).</x-alert></div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="card overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Proveedor</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $purchaseOrder->supplier?->business_name }}</p>
                        <p class="text-sm text-slate-600">RUC {{ $purchaseOrder->supplier?->ruc }}</p>
                        <p class="text-sm text-slate-600">{{ $purchaseOrder->supplier?->contact_name ?? 'Sin contacto' }} · {{ $purchaseOrder->supplier?->phone ?? 'Sin teléfono' }}</p>
                        <p class="text-sm text-slate-600">{{ $purchaseOrder->supplier?->email ?? 'Sin correo' }}</p>
                    </div>
                    <div class="sm:text-right">
                        <x-status-badge :color="$purchaseOrder->status_color">{{ $purchaseOrder->status_label }}</x-status-badge>
                        <p class="mt-2 text-xs text-slate-500">Pedido: {{ $purchaseOrder->ordered_at?->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500">Esperado: {{ $purchaseOrder->expected_at?->format('d/m/Y') }}</p>
                        @if ($purchaseOrder->received_at)
                            <p class="text-xs text-emerald-600">Recibido: {{ $purchaseOrder->received_at->format('d/m/Y') }}</p>
                        @endif
                        <p class="text-xs text-slate-500">Registrado por: {{ $purchaseOrder->user?->name }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Costo unit.</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->items as $item)
                                <tr>
                                    <td>
                                        <p class="font-medium text-slate-800">{{ $item->product_name }}</p>
                                        @if ($item->product)
                                            <p class="text-xs text-slate-500">{{ $item->product->sku }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">S/ {{ number_format((float) $item->unit_cost, 2) }}</td>
                                    <td class="text-right font-semibold">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t border-slate-100 p-5">
                    <dl class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600"><dt>Ítems</dt><dd>{{ $purchaseOrder->items->sum('quantity') }} unidades</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>S/ {{ number_format((float) $purchaseOrder->total, 2) }}</dd></div>
                    </dl>
                </div>

                @if ($purchaseOrder->notes)
                    <div class="border-t border-slate-100 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Observaciones</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $purchaseOrder->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <aside class="space-y-4">
            @if ($purchaseOrder->status !== 'recibida')
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Verificar recepción</h2>
                    <p class="mt-1 text-xs text-slate-500">Al confirmar, se incrementará el stock de cada producto y se actualizará su costo.</p>
                    <form method="POST" action="{{ route('purchases.receive', $purchaseOrder) }}" class="mt-3 grid gap-3" onsubmit="return confirm('¿Confirmas la recepción completa de esta orden?')">
                        @csrf
                        <x-input name="received_at" label="Fecha de recepción" type="date" :value="now()->toDateString()" />
                        <x-textarea name="notes" label="Observaciones" rows="2" />
                        <button type="submit" class="btn-primary w-full"><x-icon name="check" class="h-4 w-4" /> Confirmar recepción</button>
                    </form>
                </div>
            @else
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Recepción confirmada</h2>
                    <p class="mt-2 text-sm text-slate-600">La orden fue recibida el {{ $purchaseOrder->received_at?->format('d/m/Y') }} y el stock fue actualizado.</p>
                </div>
            @endif

            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Resumen</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Productos</dt><dd>{{ $purchaseOrder->items->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Unidades</dt><dd>{{ $purchaseOrder->items->sum('quantity') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Total</dt><dd class="font-bold text-primary-700">S/ {{ number_format((float) $purchaseOrder->total, 2) }}</dd></div>
                </dl>
            </div>
        </aside>
    </div>
@endsection
