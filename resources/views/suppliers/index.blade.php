@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
    <x-page-header title="Proveedores" subtitle="Gestiona los aliados de abastecimiento y sus plazos de entrega.">
        <a href="{{ route('suppliers.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nuevo proveedor</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por RUC, razón social o contacto…" class="input pl-9">
                </div>
                <select name="status" class="input sm:w-40">
                    <option value="">Todos</option>
                    <option value="active" @selected(request('status') === 'active')>Activos</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                </select>
                <button type="submit" class="btn-secondary">Filtrar</button>
            </form>
        </div>

        @if ($suppliers->isEmpty())
            <x-empty-state title="Sin proveedores" description="Registra tus proveedores para controlar compras y reposición." icon="building">
                <a href="{{ route('suppliers.create') }}" class="btn-primary">Nuevo proveedor</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr><th>Proveedor</th><th>Contacto</th><th>Plazo</th><th>Productos</th><th>Compras</th><th>Estado</th><th class="text-right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $supplier->business_name }}</a>
                                    <p class="text-xs text-slate-500">RUC {{ $supplier->ruc }}</p>
                                </td>
                                <td>
                                    <p class="text-sm text-slate-700">{{ $supplier->contact_name ?: '—' }}</p>
                                    <p class="text-xs text-slate-500">{{ $supplier->phone ?: $supplier->email ?: 'Sin contacto' }}</p>
                                </td>
                                <td><span class="badge bg-sky-100 text-sky-700">{{ $supplier->lead_time_days }} días</span></td>
                                <td><span class="badge bg-slate-100 text-slate-600">{{ $supplier->products_count }}</span></td>
                                <td><span class="badge bg-slate-100 text-slate-600">{{ $supplier->purchase_orders_count }}</span></td>
                                <td><x-status-badge :color="$supplier->active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">{{ $supplier->active ? 'Activo' : 'Inactivo' }}</x-status-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-ghost px-2.5 py-2" title="Editar"><x-icon name="edit" class="h-4 w-4" /></a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('¿Dar de baja este proveedor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost px-2.5 py-2 text-rose-600 hover:bg-rose-50" title="Dar de baja"><x-icon name="trash" class="h-4 w-4" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $suppliers->links() }}</div>
        @endif
    </div>
@endsection
