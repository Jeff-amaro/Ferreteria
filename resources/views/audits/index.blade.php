@extends('layouts.app')

@section('title', 'Auditorías')

@section('content')
    <x-page-header title="Auditorías de inventario" subtitle="Revisa y resuelve los desbalances detectados en los conteos físicos.">
        <x-status-badge color="bg-amber-100 text-amber-700">{{ $pendingCount }} abiertas</x-status-badge>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-4">
                <div class="relative md:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Código o motivo..." class="input pl-9">
                </div>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" @selected(request('status') === 'pendiente')>Pendiente</option>
                    <option value="en_proceso" @selected(request('status') === 'en_proceso')>En proceso</option>
                    <option value="resuelta" @selected(request('status') === 'resuelta')>Resuelta</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('audits.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($audits->isEmpty())
            <x-empty-state title="No hay auditorías" description="Las diferencias detectadas en los conteos físicos generarán auditorías aquí." icon="alert" />
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($audits as $audit)
                    <div class="p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $audit->code }}</p>
                                    <x-status-badge :color="$audit->status_color">{{ $audit->status_label }}</x-status-badge>
                                    @if ($audit->inventoryCount)
                                        <a href="{{ route('inventory.show', $audit->inventoryCount) }}" class="text-xs font-semibold text-primary-600 hover:underline">{{ $audit->inventoryCount->code }}</a>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-700">{{ $audit->reason }}</p>
                                <p class="text-xs text-slate-500">Solicitada por {{ $audit->requester?->name }} el {{ $audit->created_at->format('d/m/Y H:i') }}</p>
                                @if ($audit->notes)
                                    <p class="text-sm text-slate-600">{{ $audit->notes }}</p>
                                @endif
                                @if ($audit->status === 'resuelta')
                                    <p class="text-xs text-slate-500">Resuelta por {{ $audit->resolver?->name }} el {{ $audit->resolved_at?->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>

                            @if ($audit->status !== 'resuelta')
                                <form method="POST" action="{{ route('audits.resolve', $audit) }}" class="grid w-full gap-2 rounded-xl bg-slate-50 p-4 lg:w-80">
                                    @csrf
                                    <div>
                                        <label class="label" for="status-{{ $audit->id }}">Actualizar estado</label>
                                        <select name="status" id="status-{{ $audit->id }}" class="input" required>
                                            <option value="en_proceso" @selected($audit->status === 'en_proceso')>En proceso</option>
                                            <option value="resuelta">Resolver y ajustar stock</option>
                                        </select>
                                    </div>
                                    <input type="text" name="notes" class="input" placeholder="Comentario de la revisión">
                                    <button type="submit" class="btn-primary w-full">Guardar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-100 p-4">{{ $audits->links() }}</div>
        @endif
    </div>
@endsection
