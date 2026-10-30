@extends('layouts.app')

@section('title', 'Alertas')

@section('content')
    <x-page-header title="Alertas" subtitle="Desbalances de inventario, retrasos de proveedores y stock bajo.">
        @if ($pendingCount > 0)
            <form method="POST" action="{{ route('alerts.readAll') }}">
                @csrf
                <button type="submit" class="btn-secondary"><x-icon name="check" class="h-4 w-4" /> Marcar todas como leídas</button>
            </form>
        @endif
        <x-status-badge color="bg-amber-100 text-amber-700">{{ $pendingCount }} pendientes</x-status-badge>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-4">
                <select name="type" class="input">
                    <option value="">Todos los tipos</option>
                    <option value="desbalance" @selected(request('type') === 'desbalance')>Desbalance</option>
                    <option value="retraso" @selected(request('type') === 'retraso')>Retraso</option>
                    <option value="stock_bajo" @selected(request('type') === 'stock_bajo')>Stock bajo</option>
                </select>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" @selected(request('status') === 'pendiente')>Pendientes</option>
                    <option value="leida" @selected(request('status') === 'leida')>Leídas</option>
                </select>
                <div class="flex gap-2 md:col-span-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['type', 'status']))
                        <a href="{{ route('alerts.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($alerts->isEmpty())
            <x-empty-state title="No hay alertas" description="Las alertas de inventario y proveedores aparecerán aquí." icon="check" />
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($alerts as $alert)
                    @php
                        $link = match ($alert->alertable_type) {
                            \App\Models\InventoryCount::class => $alert->alertable_id ? route('inventory.show', $alert->alertable_id) : null,
                            \App\Models\PurchaseOrder::class => $alert->alertable_id ? route('purchases.show', $alert->alertable_id) : null,
                            default => null,
                        };
                    @endphp
                    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-start sm:justify-between {{ $alert->status === 'pendiente' ? '' : 'opacity-60' }}">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $alert->type_color }}">
                                <x-icon name="alert" class="h-5 w-5" />
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $alert->title }}</p>
                                    <x-status-badge :color="$alert->type_color">{{ $alert->type_label }}</x-status-badge>
                                    <x-status-badge :color="$alert->status === 'pendiente' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600'">{{ ucfirst($alert->status) }}</x-status-badge>
                                </div>
                                <p class="mt-1 text-sm text-slate-600">{{ $alert->message }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $alert->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            @if ($link)
                                <a href="{{ $link }}" class="btn-secondary">Ver detalle</a>
                            @endif
                            @if ($alert->status === 'pendiente')
                                <form method="POST" action="{{ route('alerts.read', $alert) }}">
                                    @csrf
                                    <button type="submit" class="btn-ghost text-emerald-600 hover:bg-emerald-50">Marcar leída</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-100 p-4">{{ $alerts->links() }}</div>
        @endif
    </div>
@endsection
