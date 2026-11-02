@extends('layouts.app')

@section('title', 'Mis entregas')

@section('content')
    <x-page-header title="Mis entregas" subtitle="Pedidos asignados a tu ruta, incidencias y confirmaciones.">
        <a href="{{ route('profile.edit') }}" class="btn-secondary">
            <x-icon name="user" class="h-4 w-4" /> Mi perfil
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Pedidos asignados" :value="$stats['assigned']" hint="Pendientes de entrega" icon="truck" />
        <x-stat-card label="En camino" :value="$stats['on_route']" hint="Despachos activos" icon="refresh" color="bg-sky-50 text-sky-600" />
        <x-stat-card label="Entregados" :value="$stats['delivered']" hint="Historial de entregas" icon="check" color="bg-emerald-50 text-emerald-600" />
        <x-stat-card label="Con incidencia" :value="$stats['incidents']" hint="Requieren seguimiento" icon="alert" color="bg-rose-50 text-rose-600" />
    </div>

    <div class="mt-6 card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Código, cliente, dirección o teléfono..." class="input pl-9">
                </div>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    @foreach (['en_preparacion', 'despachado', 'en_camino', 'con_incidencia', 'entregado'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\SalesOrder::STATUSES[$status] }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('delivery.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($orders->isEmpty())
            <x-empty-state title="No tienes pedidos asignados" description="Cuando el equipo de ventas te asigne un despacho, aparecerá en este panel." icon="truck" />
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Cliente y entrega</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('delivery.show', $order) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $order->code }}</a>
                                    <p class="text-xs text-slate-500">{{ $order->items->sum('quantity') }} unidades</p>
                                </td>
                                <td>
                                    <p class="text-sm font-medium text-slate-700">{{ $order->client?->name }}</p>
                                    <p class="max-w-xs text-xs text-slate-500">{{ $order->delivery_address ?: 'Dirección por confirmar' }}</p>
                                    @if ($order->client?->phone)
                                        <p class="text-xs text-slate-500">{{ $order->client->phone }}</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-600">
                                    <p>Estimada: {{ $order->delivery_date?->format('d/m/Y') ?? 'Por definir' }}</p>
                                    @if ($order->fecha_entrega)
                                        <p class="text-xs text-emerald-600">Entregado: {{ $order->fecha_entrega->format('d/m/Y H:i') }}</p>
                                    @elseif ($order->fecha_despacho)
                                        <p class="text-xs text-sky-600">Despacho: {{ $order->fecha_despacho->format('d/m/Y H:i') }}</p>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge>
                                    @if ($order->incidencia_nota)
                                        <p class="mt-1 max-w-xs truncate text-xs text-rose-600">{{ $order->incidencia_nota }}</p>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('delivery.show', $order) }}" class="btn-ghost px-2.5 py-2" title="Verificar datos de entrega">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                        @if (in_array($order->status, ['despachado', 'en_camino', 'con_incidencia'], true))
                                            <a href="{{ route('delivery.confirm', $order) }}" class="btn-ghost px-2.5 py-2 text-emerald-600" title="Registrar entrega">
                                                <x-icon name="check" class="h-4 w-4" />
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 p-4">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
