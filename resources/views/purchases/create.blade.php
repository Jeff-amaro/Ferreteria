@extends('layouts.app')

@section('title', 'Nueva orden de compra')

@section('content')
    <x-page-header title="Nueva orden de compra" subtitle="Solicita productos al proveedor y registra la recepción cuando lleguen.">
        <a href="{{ route('purchases.index') }}" class="btn-ghost">Volver</a>
    </x-page-header>

    <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form" class="grid gap-6 xl:grid-cols-[1fr_340px]">
        @csrf
        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Datos del pedido</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="supplier_id">Proveedor <span class="text-rose-500">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="input" required>
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>{{ $supplier->business_name }} · RUC {{ $supplier->ruc }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <x-input name="expected_at" label="Fecha esperada de entrega" type="date" required :value="old('expected_at', now()->addDays(7)->toDateString())" />
                </div>
                <div class="mt-4">
                    <label class="label" for="notes">Observaciones</label>
                    <textarea name="notes" id="notes" rows="2" class="input" placeholder="Condiciones de compra, forma de pago...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 p-5">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Productos del pedido</h2>
                    <button type="button" id="add-item" class="btn-secondary"><x-icon name="plus" class="h-4 w-4" /> Agregar producto</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base" id="items-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="w-28 text-center">Cantidad</th>
                                <th class="w-32 text-center">Costo unit.</th>
                                <th class="w-28 text-right">Subtotal</th>
                                <th class="w-12"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 p-5">
                    <p class="text-sm text-slate-500" id="items-count">0 productos</p>
                    <p class="text-lg font-bold text-slate-900">Total: <span class="text-primary-700" id="purchase-total">S/ 0.00</span></p>
                </div>
                @error('items')<p class="px-5 pb-4 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <aside class="card h-fit p-6 xl:sticky xl:top-24">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Resumen</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Proveedor</dt><dd class="text-right font-medium" id="summary-supplier">Sin seleccionar</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Productos</dt><dd id="summary-items">0</dd></div>
                <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold"><dt>Total</dt><dd class="text-primary-700" id="summary-total">S/ 0.00</dd></div>
            </dl>
            <button type="submit" class="btn-primary mt-5 w-full"><x-icon name="check" class="h-4 w-4" /> Registrar orden</button>
            <p class="mt-2 text-center text-[11px] text-slate-500">El stock se actualizará al verificar la recepción.</p>
        </aside>
    </form>

    <template id="item-template">
        <tr>
            <td>
                <select class="input product-select" required>
                    <option value="">Selecciona un producto</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-cost="{{ (float) $product->cost }}" data-supplier="{{ $product->supplier_id }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" class="input quantity-input text-center" min="1" step="1" value="1" required></td>
            <td><input type="number" class="input cost-input text-center" min="0" step="0.01" value="0" required></td>
            <td class="text-right font-semibold subtotal-cell">S/ 0.00</td>
            <td>
                <button type="button" class="btn-ghost px-2 py-2 text-rose-600 remove-item" title="Quitar"><x-icon name="trash" class="h-4 w-4" /></button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
<script>
    const tableBody = document.querySelector('#items-table tbody');
    const template = document.getElementById('item-template');
    const formatter = new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const supplierSelect = document.getElementById('supplier_id');

    function money(value) {
        return 'S/ ' + formatter.format(Number(value) || 0);
    }

    function addRow() {
        const clone = template.content.cloneNode(true);
        tableBody.appendChild(clone);
        bindRow(tableBody.lastElementChild);
        reindex();
        updateTotals();
    }

    function bindRow(row) {
        const product = row.querySelector('.product-select');
        const quantity = row.querySelector('.quantity-input');
        const cost = row.querySelector('.cost-input');

        product.addEventListener('change', () => {
            const option = product.selectedOptions[0];
            const supplier = option?.dataset.supplier;

            if (supplier && !supplierSelect.value) {
                supplierSelect.value = supplier;
            }

            if (option?.dataset.cost) {
                cost.value = Number(option.dataset.cost).toFixed(2);
            }

            updateTotals();
        });

        quantity.addEventListener('input', updateTotals);
        cost.addEventListener('input', updateTotals);

        row.querySelector('.remove-item').addEventListener('click', () => {
            row.remove();
            if (tableBody.children.length === 0) addRow();
            reindex();
            updateTotals();
        });
    }

    function reindex() {
        [...tableBody.children].forEach((row, index) => {
            row.querySelector('.product-select').name = `items[${index}][product_id]`;
            row.querySelector('.quantity-input').name = `items[${index}][quantity]`;
            row.querySelector('.cost-input').name = `items[${index}][unit_cost]`;
        });
    }

    function updateTotals() {
        let total = 0;
        let count = 0;

        [...tableBody.children].forEach((row) => {
            const quantity = Number(row.querySelector('.quantity-input').value) || 0;
            const cost = Number(row.querySelector('.cost-input').value) || 0;
            const subtotal = quantity * cost;

            row.querySelector('.subtotal-cell').textContent = money(subtotal);
            total += subtotal;
            if (row.querySelector('.product-select').value) count++;
        });

        document.getElementById('purchase-total').textContent = money(total);
        document.getElementById('summary-total').textContent = money(total);
        document.getElementById('summary-items').textContent = count;
        document.getElementById('items-count').textContent = count + ' productos';
        document.getElementById('summary-supplier').textContent = supplierSelect.selectedOptions[0]?.textContent?.split('·')[0]?.trim() || 'Sin seleccionar';
    }

    document.getElementById('add-item').addEventListener('click', addRow);
    supplierSelect.addEventListener('change', updateTotals);

    addRow();
</script>
@endpush
