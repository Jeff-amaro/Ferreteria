@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <x-page-header title="Clientes" subtitle="Cartera de clientes minoristas y mayoristas.">
        <a href="{{ route('clients.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nuevo cliente</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por DNI/RUC, nombre o correo…" class="input pl-9">
                </div>
                <select name="type" class="input sm:w-44">
                    <option value="">Todos los tipos</option>
                    <option value="minorista" @selected(request('type') === 'minorista')>Minorista</option>
                    <option value="mayorista" @selected(request('type') === 'mayorista')>Mayorista</option>
                </select>
                <select name="status" class="input sm:w-40">
                    <option value="">Todos</option>
                    <option value="active" @selected(request('status') === 'active')>Activos</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                </select>
                <button type="submit" class="btn-secondary">Filtrar</button>
            </form>
        </div>

        @if ($clients->isEmpty())
            <x-empty-state title="Sin clientes" description="Registra el primer cliente para iniciar una cotización." icon="users">
                <a href="{{ route('clients.create') }}" class="btn-primary">Nuevo cliente</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr><th>Cliente</th><th>Documento</th><th>Tipo</th><th>Contacto</th><th>Órdenes</th><th>Estado</th><th class="text-right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>
                                    <a href="{{ route('clients.show', $client) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $client->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $client->email ?: 'Sin correo' }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-slate-100 text-slate-600">{{ $client->document_type }}</span>
                                    <span class="ml-1 font-medium text-slate-700">{{ $client->document_number }}</span>
                                </td>
                                <td>
                                    <x-status-badge :color="$client->is_wholesale ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'">{{ $client->type_label }}</x-status-badge>
                                </td>
                                <td>
                                    <p class="text-sm text-slate-700">{{ $client->phone ?: '—' }}</p>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($client->address, 40) ?: 'Sin dirección' }}</p>
                                </td>
                                <td><span class="badge bg-slate-100 text-slate-600">{{ $client->sales_orders_count }}</span></td>
                                <td><x-status-badge :color="$client->active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">{{ $client->active ? 'Activo' : 'Inactivo' }}</x-status-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('clients.show', $client) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn-ghost px-2.5 py-2" title="Editar"><x-icon name="edit" class="h-4 w-4" /></a>
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Dar de baja este cliente?')">
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
            <div class="border-t border-slate-100 px-4 py-3">{{ $clients->links() }}</div>
        @endif
    </div>
@endsection
