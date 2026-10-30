@extends('layouts.app')

@section('title', 'Conteo físico')

@section('content')
    <x-page-header title="Conteo físico de inventario" subtitle="Registra el stock real, detecta diferencias y genera auditorías automáticamente.">
        <a href="{{ route('replenishment.index') }}" class="btn-secondary"><x-icon name="refresh" class="h-4 w-4" /> Reposición</a>
        <a href="{{ route('inventory.create') }}" class="btn-primary"><x-icon name="clipboard" class="h-4 w-4" /> Nuevo conteo</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-56">
                    <label class="label" for="status">Estado</label>
                    <select name="status" id="status" class="input">
                        <option value="">Todos los conteos</option>
                        <option value="borrador" @selected(request('status') === 'borrador')>Borrador</option>
                        <option value="completado" @selected(request('status') === 'completado')>Completado</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filtrar</button>
                @if (request()->filled('status'))
                    <a href="{{ route('inventory.index') }}" class="btn-ghost">Limpiar</a>
                @endif
            </form>
        </div>

        @if ($counts->isEmpty())
            <x-empty-state title="Aún no hay conteos físicos" description="Inicia un conteo para comparar el stock del sistema con el stock real." icon="clipboard">
                <a href="{{ route('inventory.create') }}" class="btn-primary">Iniciar conteo</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Responsable</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Diferencias</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($counts as $count)
                            <tr>
                                <td>
                                    <a href="{{ route('inventory.show', $count) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $count->code }}</a>
                                    @if ($count->notes)
                                        <p class="max-w-xs truncate text-xs text-slate-500">{{ $count->notes }}</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-600">{{ $count->user?->name ?? '—' }}</td>
                                <td class="text-sm text-slate-600">{{ $count->counted_at?->format('d/m/Y H:i') ?? $count->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-sm text-slate-600">{{ $count->items_count }}</td>
                                <td>
                                    @if ($count->differences_count > 0)
                                        <x-status-badge color="bg-rose-100 text-rose-700">{{ $count->differences_count }} con diferencia</x-status-badge>
                                    @else
                                        <x-status-badge color="bg-emerald-100 text-emerald-700">Sin diferencias</x-status-badge>
                                    @endif
                                </td>
                                <td>
                                    <x-status-badge :color="$count->status === 'completado' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">
                                        {{ ucfirst($count->status) }}
                                    </x-status-badge>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('inventory.show', $count) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        <a href="{{ route('inventory.pdf', $count) }}" class="btn-ghost px-2.5 py-2" title="PDF"><x-icon name="download" class="h-4 w-4" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 p-4">{{ $counts->links() }}</div>
        @endif
    </div>
@endsection
