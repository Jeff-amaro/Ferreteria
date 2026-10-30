@extends('layouts.app')

@section('title', 'Conteo '.$inventoryCount->code)

@section('content')
    <x-page-header :title="'Conteo físico '.$inventoryCount->code" :subtitle="'Registrado por '.$inventoryCount->user?->name.' el '.$inventoryCount->counted_at?->format('d/m/Y H:i')">
        <a href="{{ route('inventory.index') }}" class="btn-ghost">Volver</a>
        <a href="{{ route('inventory.pdf', $inventoryCount) }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> PDF</a>
        @if ($inventoryCount->has_differences)
            <a href="{{ route('audits.index') }}" class="btn-primary"><x-icon name="alert" class="h-4 w-4" /> Ver auditorías</a>
        @endif
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Productos contados" :value="$inventoryCount->items->count()" icon="box" />
        <x-stat-card label="Diferencias" :value="$inventoryCount->items->where('balanced', false)->count()" icon="alert" :color="$inventoryCount->has_differences ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'" />
        <x-stat-card label="Diferencia total" :value="($inventoryCount->total_difference > 0 ? '+' : '').$inventoryCount->total_difference.' und.'" icon="refresh" />
        <x-stat-card label="Estado" :value="ucfirst($inventoryCount->status)" icon="check" color="bg-emerald-50 text-emerald-600" />
    </div>

    <div class="mt-6 card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Detalle del conteo</h2>
            @if ($inventoryCount->notes)
                <p class="mt-1 text-sm text-slate-600">{{ $inventoryCount->notes }}</p>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th class="text-center">Sistema</th>
                        <th class="text-center">Físico</th>
                        <th class="text-center">Diferencia</th>
                        <th class="text-center">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryCount->items as $item)
                        <tr>
                            <td class="font-medium text-slate-800">{{ $item->product_name }}</td>
                            <td class="text-xs text-slate-500">{{ $item->sku }}</td>
                            <td class="text-center">{{ $item->system_stock }}</td>
                            <td class="text-center font-semibold">{{ $item->physical_stock }}</td>
                            <td class="text-center font-semibold {{ $item->difference > 0 ? 'text-emerald-600' : ($item->difference < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                                {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                            </td>
                            <td class="text-center">
                                <x-status-badge :color="$item->balanced ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                                    {{ $item->balanced ? 'Cuadrado' : 'Desbalance' }}
                                </x-status-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($inventoryCount->audits->isNotEmpty())
        <div class="mt-6 card overflow-hidden">
            <div class="border-b border-slate-100 p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Auditorías generadas</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($inventoryCount->audits as $audit)
                    <div class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-slate-900">{{ $audit->code }}</p>
                                <x-status-badge :color="$audit->status_color">{{ $audit->status_label }}</x-status-badge>
                            </div>
                            <p class="text-sm text-slate-600">{{ $audit->reason }}</p>
                            <p class="text-xs text-slate-500">Solicitada por {{ $audit->requester?->name }} el {{ $audit->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('audits.index') }}" class="btn-secondary">Revisar auditoría</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
