@extends('layouts.app')

@section('title', 'Comprobante '.$receipt->code)

@push('styles')
<style>
    @media print {
        #sidebar, header, [data-sidebar-overlay], .no-print { display: none !important; }
        .lg\:pl-72 { padding-left: 0 !important; }
        main { padding: 0 !important; }
        .print-card { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="no-print">
        <x-page-header :title="'Comprobante '.$receipt->code" :subtitle="$receipt->type_label.' emitida el '.$receipt->issued_at?->format('d/m/Y H:i')">
            <a href="{{ route('orders.show', $receipt->salesOrder) }}" class="btn-ghost">Volver a la orden</a>
            <a href="{{ route('receipts.pdf', $receipt) }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> PDF</a>
            <button type="button" onclick="window.print()" class="btn-primary"><x-icon name="receipt" class="h-4 w-4" /> Imprimir</button>
        </x-page-header>
    </div>

    <div class="print-card mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 border-b-2 border-primary-600 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-2xl font-bold text-primary-700">{{ config('company.name') }}</p>
                <p class="mt-1 text-sm text-slate-500">RUC {{ config('company.ruc') }}</p>
                <p class="text-sm text-slate-500">{{ config('company.address') }}</p>
                <p class="text-sm text-slate-500">{{ config('company.phone') }} · {{ config('company.email') }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Comprobante de pago</p>
                <p class="text-xl font-bold text-slate-900">{{ $receipt->type_label }}</p>
                <p class="text-lg font-bold text-primary-700">{{ $receipt->code }}</p>
                <p class="mt-1 text-xs text-slate-500">Emitido: {{ $receipt->issued_at?->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Cliente</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $receipt->salesOrder?->client?->name }}</p>
                <p class="text-sm text-slate-600">{{ $receipt->salesOrder?->client?->document_label }}</p>
                <p class="text-sm text-slate-600">{{ $receipt->salesOrder?->client?->address ?? 'Sin dirección' }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Orden de venta</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $receipt->salesOrder?->code }}</p>
                <p class="text-sm text-slate-600">Método: {{ $receipt->payment?->method_label }}</p>
                <p class="text-sm text-slate-600">Operación: {{ $receipt->payment?->reference ?? '—' }}</p>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receipt->salesOrder?->items ?? [] as $item)
                        <tr>
                            <td>
                                <p class="font-medium text-slate-800">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-500">{{ $item->sku }}</p>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-right font-semibold">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <dl class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex justify-between text-slate-600"><dt>Subtotal</dt><dd>S/ {{ number_format((float) $receipt->salesOrder?->subtotal, 2) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Descuento</dt><dd class="text-emerald-600">- S/ {{ number_format((float) $receipt->salesOrder?->discount, 2) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Envío</dt><dd>S/ {{ number_format((float) $receipt->salesOrder?->shipping, 2) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>IGV (18%)</dt><dd>S/ {{ number_format((float) $receipt->salesOrder?->igv, 2) }}</dd></div>
                <div class="flex justify-between border-t-2 border-primary-600 pt-2 text-lg font-bold text-primary-700"><dt>Total</dt><dd>S/ {{ number_format((float) $receipt->total, 2) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Monto recibido</dt><dd>S/ {{ number_format((float) $receipt->payment?->amount, 2) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Vuelto</dt><dd>S/ {{ number_format((float) $receipt->payment?->change_amount, 2) }}</dd></div>
            </dl>
        </div>

        <p class="mt-8 border-t border-slate-200 pt-4 text-center text-xs text-slate-500">
            Gracias por su compra. Este comprobante fue generado electrónicamente por {{ config('company.name') }}.
        </p>
    </div>
@endsection
