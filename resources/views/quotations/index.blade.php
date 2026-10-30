@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
    <x-page-header title="Cotizaciones" subtitle="Consulta, emite y convierte las cotizaciones generadas en el punto de venta.">
        <a href="{{ route('pos.index') }}" class="btn-primary"><x-icon name="cart" class="h-4 w-4" /> Nueva venta</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-4">
                <div class="relative md:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Código, cliente o documento..." class="input pl-9">
                </div>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Quotation::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('quotations.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($quotations->isEmpty())
            <x-empty-state title="No hay cotizaciones" description="Registra una venta en el punto de venta para generar la primera cotización." icon="document">
                <a href="{{ route('pos.index') }}" class="btn-primary">Ir al punto de venta</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Emisión</th>
                            <th>Vigencia</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotations as $quotation)
                            <tr>
                                <td>
                                    <a href="{{ route('quotations.show', $quotation) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $quotation->code }}</a>
                                    <p class="text-xs text-slate-500">{{ $quotation->items()->count() }} ítems</p>
                                </td>
                                <td>
                                    <p class="text-sm font-medium text-slate-700">{{ $quotation->client?->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $quotation->client?->document_label }}</p>
                                </td>
                                <td class="text-sm text-slate-600">{{ $quotation->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-sm text-slate-600">{{ $quotation->valid_until?->format('d/m/Y') ?? '—' }}</td>
                                <td class="font-semibold text-slate-900">S/ {{ number_format((float) $quotation->total, 2) }}</td>
                                <td><x-status-badge :color="$quotation->status_color">{{ $quotation->status_label }}</x-status-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('quotations.show', $quotation) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        <a href="{{ route('quotations.pdf', $quotation) }}" class="btn-ghost px-2.5 py-2" title="PDF"><x-icon name="download" class="h-4 w-4" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 p-4">{{ $quotations->links() }}</div>
        @endif
    </div>
@endsection
