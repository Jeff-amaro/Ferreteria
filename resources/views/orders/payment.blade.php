@extends('layouts.app')

@section('title', 'Registrar pago '.$order->code)

@section('content')
    <x-page-header :title="'Registrar pago · '.$order->code" subtitle="Confirma el método de pago y emite el comprobante correspondiente.">
        <a href="{{ route('orders.show', $order) }}" class="btn-ghost">Volver a la orden</a>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
        <div class="card p-6">
            <form method="POST" action="{{ route('orders.payment.store', $order) }}" id="payment-form" class="space-y-5">
                @csrf

                <div>
                    <p class="label">Método de pago</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach (\App\Models\Payment::METHODS as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-primary-300 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                <input type="radio" name="method" value="{{ $value }}" class="text-primary-600 focus:ring-primary-500" @checked($loop->first) required>
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">{{ $label }}</span>
                                    <span class="block text-xs text-slate-500">{{ $value === 'efectivo' ? 'Calcula el vuelto automáticamente' : 'Pago electrónico' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('method')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="amount">Monto recibido (S/) <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount" id="amount" min="0.01" step="0.01" value="{{ old('amount', number_format((float) $order->balance, 2, '.', '')) }}" class="input" required>
                        @error('amount')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label" for="reference">Número de operación / referencia</label>
                        <input type="text" name="reference" id="reference" value="{{ old('reference') }}" class="input" placeholder="Obligatorio para pagos electrónicos">
                        @error('reference')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>Saldo pendiente</span>
                        <span class="font-semibold text-slate-900">S/ {{ number_format((float) $order->balance, 2) }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm text-slate-600">
                        <span>Vuelto estimado</span>
                        <span class="font-semibold text-emerald-600" id="change-preview">S/ 0.00</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('orders.show', $order) }}" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary"><x-icon name="wallet" class="h-4 w-4" /> Confirmar pago y emitir comprobante</button>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Resumen de la orden</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Cliente</dt><dd class="text-right font-medium">{{ $order->client?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Documento</dt><dd>{{ $order->client?->document_label }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Comprobante</dt><dd>{{ $order->client?->document_type === 'RUC' ? 'Factura' : 'Boleta' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Ítems</dt><dd>{{ $order->items->count() }}</dd></div>
                    <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>S/ {{ number_format((float) $order->total, 2) }}</dd></div>
                </dl>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    const amount = document.getElementById('amount');
    const methodInputs = document.querySelectorAll('input[name="method"]');
    const reference = document.getElementById('reference');
    const balance = {{ (float) $order->balance }};
    const formatter = new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function updatePaymentUi() {
        const method = document.querySelector('input[name="method"]:checked')?.value;
        const value = Number(amount.value) || 0;
        const change = method === 'efectivo' ? Math.max(0, value - balance) : 0;

        document.getElementById('change-preview').textContent = 'S/ ' + formatter.format(change);
        reference.required = method !== 'efectivo';

        if (method !== 'efectivo' && Math.abs(value - balance) > 0.001) {
            amount.value = balance.toFixed(2);
        }
    }

    amount.addEventListener('input', updatePaymentUi);
    methodInputs.forEach((input) => input.addEventListener('change', updatePaymentUi));
    updatePaymentUi();
</script>
@endpush
