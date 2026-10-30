@extends('layouts.app')

@section('title', 'Ficha de cliente')

@section('content')
    <x-page-header title="Ficha de cliente" subtitle="Historial comercial y datos de contacto.">
        <a href="{{ route('clients.edit', $client) }}" class="btn-primary"><x-icon name="edit" class="h-4 w-4" /> Editar</a>
        <a href="{{ route('clients.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-100 text-lg font-bold text-primary-700">{{ mb_substr($client->name, 0, 2) }}</span>
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-bold text-slate-900">{{ $client->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $client->document_label }}</p>
                    <span class="badge mt-1 {{ $client->is_wholesale ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $client->type_label }}</span>
                </div>
            </div>
            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Correo</dt><dd class="text-right font-medium text-slate-800">{{ $client->email ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Teléfono</dt><dd class="font-medium text-slate-800">{{ $client->phone ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Dirección</dt><dd class="text-right font-medium text-slate-800">{{ $client->address ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Estado</dt><dd><x-status-badge :color="$client->active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">{{ $client->active ? 'Activo' : 'Inactivo' }}</x-status-badge></dd></div>
            </dl>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-3 text-center"><p class="text-2xl font-bold text-slate-900">{{ $client->sales_orders_count }}</p><p class="text-xs text-slate-500">Órdenes</p></div>
                <div class="rounded-xl bg-slate-50 p-3 text-center"><p class="text-2xl font-bold text-slate-900">{{ $client->quotations_count }}</p><p class="text-xs text-slate-500">Cotizaciones</p></div>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-bold text-slate-900">Últimas órdenes</h3></div>
                @if ($client->salesOrders->isEmpty())
                    <x-empty-state title="Sin órdenes" description="Este cliente aún no registra ventas." icon="receipt" />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead><tr><th>Orden</th><th>Fecha</th><th>Estado</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                @foreach ($client->salesOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('orders.show', $order) }}" class="font-semibold text-primary-600">{{ $order->code }}</a></td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></td>
                                        <td class="text-right font-semibold">S/ {{ number_format((float) $order->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-bold text-slate-900">Últimas cotizaciones</h3></div>
                @if ($client->quotations->isEmpty())
                    <x-empty-state title="Sin cotizaciones" description="Este cliente aún no solicita cotizaciones." icon="document" />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead><tr><th>Cotización</th><th>Fecha</th><th>Estado</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                @foreach ($client->quotations as $quotation)
                                    <tr>
                                        <td><a href="{{ route('quotations.show', $quotation) }}" class="font-semibold text-primary-600">{{ $quotation->code }}</a></td>
                                        <td>{{ $quotation->created_at->format('d/m/Y') }}</td>
                                        <td><x-status-badge :color="$quotation->status_color">{{ $quotation->status_label }}</x-status-badge></td>
                                        <td class="text-right font-semibold">S/ {{ number_format((float) $quotation->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
