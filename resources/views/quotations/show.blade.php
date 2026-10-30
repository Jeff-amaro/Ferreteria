@extends('layouts.app')

@section('title', 'Cotización '.$quotation->code)

@section('content')
    <x-page-header :title="'Cotización '.$quotation->code" :subtitle="'Emitida el '.$quotation->created_at->format('d/m/Y H:i').' por '.$quotation->user?->name">
        <a href="{{ route('quotations.index') }}" class="btn-ghost">Volver</a>
        <a href="{{ route('quotations.pdf', $quotation) }}" class="btn-secondary"><x-icon name="download" class="h-4 w-4" /> PDF</a>
        @if ($quotation->status === 'borrador')
            <form method="POST" action="{{ route('quotations.emit', $quotation) }}">
                @csrf
                <button type="submit" class="btn-secondary"><x-icon name="check" class="h-4 w-4" /> Emitir</button>
            </form>
        @endif
        @if (! $quotation->is_converted)
            <form method="POST" action="{{ route('quotations.convert', $quotation) }}" onsubmit="return confirm('¿Convertir esta cotización en orden de venta? Se reservará el stock.')">
                @csrf
                <button type="submit" class="btn-primary"><x-icon name="arrow-right" class="h-4 w-4" /> Convertir a orden</button>
            </form>
        @elseif ($quotation->salesOrder)
            <a href="{{ route('orders.show', $quotation->salesOrder) }}" class="btn-primary"><x-icon name="receipt" class="h-4 w-4" /> Ver orden {{ $quotation->salesOrder->code }}</a>
        @endif
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <div class="card overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cliente</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $quotation->client?->name }}</p>
                        <p class="text-sm text-slate-600">{{ $quotation->client?->document_label }}</p>
                        <p class="text-sm text-slate-600">{{ $quotation->client?->phone ?? 'Sin teléfono' }} · {{ $quotation->client?->email ?? 'Sin correo' }}</p>
                        <p class="text-sm text-slate-600">{{ $quotation->client?->address ?? 'Sin dirección' }}</p>
                    </div>
                    <div class="sm:text-right">
                        <x-status-badge :color="$quotation->status_color">{{ $quotation->status_label }}</x-status-badge>
                        <p class="mt-2 text-xs text-slate-500">Vigencia: {{ $quotation->valid_until?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                        <p class="text-xs text-slate-500">Tipo: {{ $quotation->client?->type_label }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right">Descuento</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotation->items as $item)
                                <tr>
                                    <td>
                                        <p class="font-medium text-slate-800">{{ $item->product_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->sku }}</p>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-right text-emerald-600">- S/ {{ number_format((float) $item->discount, 2) }}</td>
                                    <td class="text-right font-semibold">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end border-t border-slate-100 p-5">
                    <dl class="w-full max-w-xs space-y-2 text-sm">
                        <div class="flex justify-between text-slate-600"><dt>Subtotal</dt><dd>S/ {{ number_format((float) $quotation->subtotal, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>Descuento</dt><dd class="text-emerald-600">- S/ {{ number_format((float) $quotation->discount, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>Envío</dt><dd>S/ {{ number_format((float) $quotation->shipping, 2) }}</dd></div>
                        <div class="flex justify-between text-slate-600"><dt>IGV (18%)</dt><dd>S/ {{ number_format((float) $quotation->igv, 2) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>S/ {{ number_format((float) $quotation->total, 2) }}</dd></div>
                    </dl>
                </div>
            </div>

            @if ($quotation->notes)
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Observaciones</h2>
                    <p class="mt-2 text-sm text-slate-700">{{ $quotation->notes }}</p>
                </div>
            @endif

            @if ($quotation->wholesaleRequests->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="border-b border-slate-100 p-5">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Solicitudes mayoristas</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach ($quotation->wholesaleRequests as $wholesale)
                            <div class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Descuento solicitado: {{ number_format((float) $wholesale->requested_discount, 2) }}%</p>
                                    <p class="text-xs text-slate-500">Solicitado por {{ $wholesale->requester?->name }} el {{ $wholesale->created_at->format('d/m/Y H:i') }}</p>
                                    @if ($wholesale->notes)
                                        <p class="mt-1 text-xs text-slate-600">{{ $wholesale->notes }}</p>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right">
                                    <x-status-badge :color="$wholesale->status_color">{{ $wholesale->status_label }}</x-status-badge>
                                    @if ($wholesale->approved_discount)
                                        <p class="mt-1 text-xs text-slate-500">Aprobado: {{ number_format((float) $wholesale->approved_discount, 2) }}%</p>
                                    @endif
                                    @if ($wholesale->evaluator)
                                        <p class="text-xs text-slate-500">Por {{ $wholesale->evaluator->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Acciones</h2>
                <div class="mt-3 grid gap-2">
                    @if (in_array($quotation->status, ['emitida', 'borrador'], true))
                        <form method="POST" action="{{ route('quotations.status', $quotation) }}" class="grid gap-2">
                            @csrf
                            <input type="hidden" name="status" value="aceptada">
                            <button type="submit" class="btn-secondary w-full"><x-icon name="check" class="h-4 w-4" /> Marcar como aceptada</button>
                        </form>
                        <form method="POST" action="{{ route('quotations.status', $quotation) }}">
                            @csrf
                            <input type="hidden" name="status" value="rechazada">
                            <button type="submit" class="btn-ghost w-full text-rose-600 hover:bg-rose-50"><x-icon name="x" class="h-4 w-4" /> Marcar como rechazada</button>
                        </form>
                    @endif
                </div>
            </div>

            @if (! $quotation->is_converted && ! $quotation->wholesaleRequests->where('status', 'pendiente')->count())
                <div class="card p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Solicitar evaluación mayorista</h2>
                    <p class="mt-1 text-xs text-slate-500">El administrador evaluará el descuento solicitado y actualizará la cotización si se aprueba.</p>
                    <form method="POST" action="{{ route('quotations.wholesale', $quotation) }}" class="mt-3 grid gap-3">
                        @csrf
                        <div>
                            <label class="label" for="requested_discount">Descuento solicitado (%)</label>
                            <input type="number" name="requested_discount" id="requested_discount" min="0" max="30" step="0.5" value="{{ old('requested_discount', 5) }}" class="input" required>
                        </div>
                        <div>
                            <label class="label" for="wholesale_notes">Justificación</label>
                            <textarea name="notes" id="wholesale_notes" rows="3" class="input" placeholder="Detalle del volumen o acuerdo comercial">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full"><x-icon name="sparkles" class="h-4 w-4" /> Enviar solicitud</button>
                    </form>
                </div>
            @endif

            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Resumen</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Ítems</dt><dd class="font-semibold">{{ $quotation->items->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Unidades</dt><dd class="font-semibold">{{ $quotation->items->sum('quantity') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Total</dt><dd class="font-bold text-primary-700">S/ {{ number_format((float) $quotation->total, 2) }}</dd></div>
                </dl>
            </div>
        </aside>
    </div>
@endsection
