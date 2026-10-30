@extends('layouts.app')

@section('title', 'Solicitudes mayoristas')

@section('content')
    <x-page-header title="Solicitudes mayoristas" subtitle="Evalúa las solicitudes de descuento por volumen enviadas desde las cotizaciones.">
        <x-status-badge color="bg-amber-100 text-amber-700">{{ $pendingCount }} pendientes</x-status-badge>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-56">
                    <label class="label" for="status">Estado</label>
                    <select name="status" id="status" class="input">
                        <option value="">Todas las solicitudes</option>
                        <option value="pendiente" @selected(request('status') === 'pendiente')>Pendientes</option>
                        <option value="aprobada" @selected(request('status') === 'aprobada')>Aprobadas</option>
                        <option value="rechazada" @selected(request('status') === 'rechazada')>Rechazadas</option>
                    </select>
                </div>
                <button type="submit" class="btn-secondary">Filtrar</button>
                @if (request()->filled('status'))
                    <a href="{{ route('wholesale.index') }}" class="btn-ghost">Limpiar</a>
                @endif
            </form>
        </div>

        @if ($requests->isEmpty())
            <x-empty-state title="No hay solicitudes mayoristas" description="Las solicitudes enviadas desde una cotización aparecerán aquí." icon="users" />
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($requests as $wholesale)
                    <div class="p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $wholesale->client?->name }}</p>
                                    <x-status-badge :color="$wholesale->status_color">{{ $wholesale->status_label }}</x-status-badge>
                                    @if ($wholesale->quotation)
                                        <a href="{{ route('quotations.show', $wholesale->quotation) }}" class="text-xs font-semibold text-primary-600 hover:underline">{{ $wholesale->quotation->code }}</a>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-600">
                                    Descuento solicitado: <strong>{{ number_format((float) $wholesale->requested_discount, 2) }}%</strong>
                                    · Total cotizado: <strong>S/ {{ number_format((float) $wholesale->quotation?->total, 2) }}</strong>
                                </p>
                                <p class="text-xs text-slate-500">Solicitado por {{ $wholesale->requester?->name }} el {{ $wholesale->created_at->format('d/m/Y H:i') }}</p>
                                @if ($wholesale->notes)
                                    <p class="text-sm text-slate-600">{{ $wholesale->notes }}</p>
                                @endif
                                @if ($wholesale->status !== 'pendiente')
                                    <p class="text-xs text-slate-500">
                                        Evaluado por {{ $wholesale->evaluator?->name ?? '—' }} el {{ $wholesale->evaluated_at?->format('d/m/Y H:i') }}
                                        @if ($wholesale->approved_discount)
                                            · Descuento aprobado: {{ number_format((float) $wholesale->approved_discount, 2) }}%
                                        @endif
                                    </p>
                                @endif
                            </div>

                            @if ($wholesale->status === 'pendiente' && auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('wholesale.evaluate', $wholesale) }}" class="grid w-full gap-2 rounded-xl bg-slate-50 p-4 lg:w-80">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="label" for="status-{{ $wholesale->id }}">Decisión</label>
                                            <select name="status" id="status-{{ $wholesale->id }}" class="input" required>
                                                <option value="aprobada">Aprobar</option>
                                                <option value="rechazada">Rechazar</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="label" for="discount-{{ $wholesale->id }}">Descuento (%)</label>
                                            <input type="number" name="approved_discount" id="discount-{{ $wholesale->id }}" min="0" max="30" step="0.5" value="{{ $wholesale->requested_discount }}" class="input">
                                        </div>
                                    </div>
                                    <input type="text" name="notes" class="input" placeholder="Comentario (opcional)">
                                    <button type="submit" class="btn-primary w-full">Guardar evaluación</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-100 p-4">{{ $requests->links() }}</div>
        @endif
    </div>
@endsection
