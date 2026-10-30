@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <x-page-header title="Categorías" subtitle="Organiza el catálogo por familias de productos.">
        <a href="{{ route('categories.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva categoría</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar categoría…" class="input pl-9">
                </div>
                <select name="status" class="input sm:w-44">
                    <option value="">Todos los estados</option>
                    <option value="active" @selected(request('status') === 'active')>Activas</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactivas</option>
                </select>
                <button type="submit" class="btn-secondary">Filtrar</button>
            </form>
        </div>

        @if ($categories->isEmpty())
            <x-empty-state title="Sin categorías" description="Crea la primera categoría para organizar tus productos." icon="tag">
                <a href="{{ route('categories.create') }}" class="btn-primary">Nueva categoría</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr><th>Categoría</th><th>Descripción</th><th>Productos</th><th>Estado</th><th class="text-right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $category->name }}</td>
                                <td class="max-w-md text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($category->description, 90) ?: '—' }}</td>
                                <td><span class="badge bg-slate-100 text-slate-600">{{ $category->products_count }}</span></td>
                                <td>
                                    <x-status-badge :color="$category->active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">{{ $category->active ? 'Activa' : 'Inactiva' }}</x-status-badge>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('categories.edit', $category) }}" class="btn-ghost px-2.5 py-2" title="Editar"><x-icon name="edit" class="h-4 w-4" /></a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost px-2.5 py-2 text-rose-600 hover:bg-rose-50" title="Eliminar"><x-icon name="trash" class="h-4 w-4" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $categories->links() }}</div>
        @endif
    </div>
@endsection
