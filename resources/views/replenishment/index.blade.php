@extends('layouts.app')

@section('title', 'Lista de reposición')

@section('content')
    <x-page-header title="Lista de reposición" subtitle="Productos con stock igual o por debajo del mínimo, agrupados por proveedor.">
        <a href="{{ route('replenishment.pdf') }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> Exportar PDF</a>
        <a href="{{ route('purchases.create') }}" class="btn-primary"><x-icon name="truck" class="h-4 w-4" /> Nueva compra</a>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Productos críticos" :value="$groups->flatMap(fn ($group) => $group['products'])->count()" icon="alert" color="bg-rose-50 text-rose-600" />
        <x-stat-card label="Proveedores involucrados" :value="$groups->count()" icon="building" />
        <x-stat-card label="Unidades sugeridas" :value="$totalUnits" icon="refresh" color="bg-emerald-50 text-emerald-600" />
    </div>

    @if ($groups->isEmpty())
        <div class="card">
            <x-empty-state title="Todo el stock está en orden" description="No hay productos por debajo del stock mínimo." icon="check" />
        </div>
    @else
        <div class="space-y-6">
            @foreach ($groups as $group)
                <div class="card overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 p-5">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $group['supplier'] }}</h2>
                            <p class="text-sm text-slate-500">{{ $group['products']->count() }} productos requieren reposición</p>
                        </div>
                        <x-status-badge color="bg-amber-100 text-amber-700">{{ $group['products']->sum('suggested_quantity') }} unidades sugeridas</x-status-badge>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>SKU</th>
                                    <th class="text-center">Stock actual</th>
                                    <th class="text-center">Stock mínimo</th>
                                    <th class="text-center">Sugerido</th>
                                    <th class="text-right">Costo estimado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['products'] as $product)
                                    <tr>
                                        <td class="font-medium text-slate-800">{{ $product->name }}</td>
                                        <td class="text-xs text-slate-500">{{ $product->sku }}</td>
                                        <td class="text-center"><x-status-badge :color="$product->stock_color">{{ $product->stock }}</x-status-badge></td>
                                        <td class="text-center text-slate-600">{{ $product->min_stock }}</td>
                                        <td class="text-center font-semibold text-primary-700">{{ $product->suggested_quantity }}</td>
                                        <td class="text-right text-slate-700">S/ {{ number_format((float) $product->cost * $product->suggested_quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
