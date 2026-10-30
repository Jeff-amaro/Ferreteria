@extends('layouts.app')

@section('title', 'Punto de venta')

@section('content')
    <x-page-header title="Punto de venta" subtitle="Registra ventas presenciales, aplica descuentos por volumen y controla el stock en tiempo real.">
        <a href="{{ route('quotations.index') }}" class="btn-secondary"><x-icon name="document" class="h-4 w-4" /> Cotizaciones</a>
        <a href="{{ route('orders.index') }}" class="btn-secondary"><x-icon name="receipt" class="h-4 w-4" /> Órdenes</a>
    </x-page-header>

    <form method="POST" action="{{ route('pos.store') }}" id="pos-form" class="grid gap-6 xl:grid-cols-[1fr_420px]">
        @csrf
        <input type="hidden" name="items" id="items-input">
        <input type="hidden" name="discount" id="discount-input" value="0">
        <input type="hidden" name="shipping" id="shipping-input" value="0">

        <div class="space-y-4">
            <div class="card p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                        <input type="search" id="product-search" placeholder="Buscar producto por nombre o SKU..." class="input pl-9" autocomplete="off">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="refresh-stock" class="btn-secondary"><x-icon name="refresh" class="h-4 w-4" /> Stock</button>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2" id="category-filters">
                    <button type="button" data-category="all" class="rounded-full bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white">Todos</button>
                    @foreach ($categories as $category)
                        <button type="button" data-category="{{ $category->id }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-200">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3" id="product-grid">
                @foreach ($products as $product)
                    <button type="button"
                        class="product-card group flex flex-col rounded-2xl border border-slate-200 bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-sku="{{ $product->sku }}"
                        data-price="{{ (float) $product->price }}"
                        data-stock="{{ $product->stock }}"
                        data-category="{{ $product->category_id }}"
                        data-search="{{ \Illuminate\Support\Str::lower($product->name.' '.$product->sku.' '.($product->brand?->name ?? '')) }}"
                        @disabled($product->stock <= 0)>
                        <div class="flex items-start justify-between gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                                <x-icon name="box" class="h-5 w-5" />
                            </span>
                            <x-status-badge :color="$product->stock_color">{{ $product->stock }} {{ $product->unit }}</x-status-badge>
                        </div>
                        <div class="mt-3 flex-1">
                            <p class="line-clamp-2 text-sm font-semibold text-slate-800">{{ $product->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} · {{ $product->brand?->name ?? 'Sin marca' }}</p>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-lg font-bold text-primary-700">S/ {{ number_format((float) $product->price, 2) }}</p>
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-500">{{ $product->category?->name ?? 'General' }}</span>
                        </div>
                    </button>
                @endforeach
            </div>

            <div id="empty-products" class="card hidden p-10 text-center text-sm text-slate-500">
                No se encontraron productos con ese criterio.
            </div>
        </div>

        <aside class="card flex h-fit flex-col overflow-hidden xl:sticky xl:top-24">
            <div class="border-b border-slate-100 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Carrito de venta</h2>
                    <button type="button" id="clear-cart" class="text-xs font-semibold text-rose-600 hover:underline">Vaciar</button>
                </div>

                <div class="mt-3">
                    <label for="client_id" class="label">Cliente <span class="text-rose-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="client_id" id="client_id" class="input" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-type="{{ $client->type }}" @selected((int) old('client_id', $selectedClientId) === $client->id)>
                                    {{ $client->name }} · {{ $client->document_label }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="open-client-modal" class="btn-secondary shrink-0 px-3" title="Registrar cliente rápido"><x-icon name="plus" class="h-4 w-4" /></button>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500" id="client-hint">Los clientes mayoristas reciben descuento automático por volumen.</p>
                </div>
            </div>

            <div class="max-h-[360px] flex-1 overflow-y-auto" id="cart-items">
                <div class="p-8 text-center text-sm text-slate-500" id="cart-empty">
                    <x-icon name="cart" class="mx-auto h-10 w-10 text-slate-300" />
                    <p class="mt-2">Agrega productos para iniciar la venta.</p>
                </div>
            </div>

            <div class="border-t border-slate-100 p-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600"><span>Subtotal</span><span id="summary-subtotal">S/ 0.00</span></div>
                    <div class="flex justify-between text-slate-600"><span>Descuento por volumen</span><span id="summary-volume" class="text-emerald-600">- S/ 0.00</span></div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="discount-field" class="text-slate-600">Descuento adicional</label>
                        <input type="number" min="0" step="0.01" id="discount-field" class="input w-28 py-1.5 text-right" value="0">
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="shipping-field" class="text-slate-600">Envío</label>
                        <input type="number" min="0" step="0.01" id="shipping-field" class="input w-28 py-1.5 text-right" value="0">
                    </div>
                    <div class="flex justify-between text-slate-600"><span>IGV (18%)</span><span id="summary-igv">S/ 0.00</span></div>
                    <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><span>Total</span><span id="summary-total">S/ 0.00</span></div>
                </div>

                <div class="mt-4 grid gap-2">
                    <input type="date" name="valid_until" value="{{ now()->addDays(15)->toDateString() }}" class="input text-sm" title="Vigencia de la cotización">
                    <textarea name="notes" rows="2" class="input text-sm" placeholder="Observaciones (opcional)">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-primary mt-4 w-full py-3" id="submit-sale">
                    <x-icon name="check" class="h-4 w-4" /> Registrar venta
                </button>
                <p class="mt-2 text-center text-[11px] text-slate-500">Se generará una cotización lista para convertir en orden de venta.</p>
            </div>
        </aside>
    </form>

    <div id="client-modal" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-slate-900/50" data-close-client-modal></div>
        <div class="absolute inset-x-4 top-10 mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Registrar cliente rápido</h2>
                    <p class="text-sm text-slate-500">El cliente quedará seleccionado para esta venta.</p>
                </div>
                <button type="button" class="btn-ghost px-2 py-1" data-close-client-modal><x-icon name="x" class="h-4 w-4" /></button>
            </div>

            <form method="POST" action="{{ route('clients.quick') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="label" for="quick_document_type">Tipo de documento</label>
                    <select name="document_type" id="quick_document_type" class="input" required>
                        <option value="DNI">DNI</option>
                        <option value="RUC">RUC</option>
                    </select>
                </div>
                <x-input name="document_number" label="Número de documento" required />
                <div class="sm:col-span-2"><x-input name="name" label="Nombre o razón social" required /></div>
                <x-input name="phone" label="Teléfono" />
                <x-input name="email" label="Correo" type="email" />
                <div class="sm:col-span-2"><x-input name="address" label="Dirección" /></div>
                <div>
                    <label class="label" for="quick_type">Tipo de cliente</label>
                    <select name="type" id="quick_type" class="input" required>
                        <option value="minorista">Minorista</option>
                        <option value="mayorista">Mayorista</option>
                    </select>
                </div>
                <input type="hidden" name="active" value="1">
                <div class="sm:col-span-2 flex justify-end gap-2">
                    <button type="button" class="btn-secondary" data-close-client-modal>Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar cliente</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    @php
        $productData = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => (float) $product->price,
            'stock' => $product->stock,
        ]);
    @endphp
    const products = @json($productData);
    const productMap = new Map(products.map((product) => [String(product.id), product]));
    const cart = new Map();

    const clientSelect = document.getElementById('client_id');
    const cartItems = document.getElementById('cart-items');
    const cartEmpty = document.getElementById('cart-empty');
    const itemsInput = document.getElementById('items-input');
    const discountField = document.getElementById('discount-field');
    const shippingField = document.getElementById('shipping-field');
    const discountInput = document.getElementById('discount-input');
    const shippingInput = document.getElementById('shipping-input');
    const productGrid = document.getElementById('product-grid');
    const emptyProducts = document.getElementById('empty-products');
    const formatter = new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const money = (value) => 'S/ ' + formatter.format(Math.max(0, Number(value) || 0));

    function isWholesale() {
        return clientSelect.selectedOptions[0]?.dataset.type === 'mayorista';
    }

    function volumeRate(quantity) {
        if (!isWholesale()) return 0;
        if (quantity >= 100) return 0.15;
        if (quantity >= 50) return 0.10;
        if (quantity >= 12) return 0.05;
        return 0;
    }

    function totals() {
        let subtotal = 0;
        let volumeDiscount = 0;

        cart.forEach((item) => {
            const gross = item.price * item.quantity;
            subtotal += gross;
            volumeDiscount += gross * volumeRate(item.quantity);
        });

        const extra = Math.min(Math.max(0, Number(discountField.value) || 0), Math.max(0, subtotal - volumeDiscount));
        const taxable = Math.max(0, subtotal - volumeDiscount - extra);
        const igv = taxable * 0.18;
        const shipping = Math.max(0, Number(shippingField.value) || 0);

        return { subtotal, volumeDiscount, extra, taxable, igv, shipping, total: taxable + igv + shipping };
    }

    function render() {
        cartItems.querySelectorAll('[data-cart-row]').forEach((row) => row.remove());
        cartEmpty.classList.toggle('hidden', cart.size > 0);

        cart.forEach((item) => {
            const row = document.createElement('div');
            row.dataset.cartRow = '1';
            row.className = 'border-b border-slate-100 p-3 last:border-0';
            row.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800">${item.name}</p>
                        <p class="text-[11px] text-slate-500">${item.sku} · ${money(item.price)} c/u</p>
                    </div>
                    <button type="button" class="text-rose-500 hover:text-rose-700" data-remove="${item.id}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <div class="inline-flex items-center rounded-lg border border-slate-200">
                        <button type="button" class="px-2 py-1 text-slate-500 hover:text-primary-600" data-dec="${item.id}">−</button>
                        <span class="w-8 text-center text-sm font-semibold">${item.quantity}</span>
                        <button type="button" class="px-2 py-1 text-slate-500 hover:text-primary-600" data-inc="${item.id}">+</button>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">${money(item.price * item.quantity * (1 - volumeRate(item.quantity)))}</p>
                        ${volumeRate(item.quantity) > 0 ? `<p class="text-[11px] font-medium text-emerald-600">-${Math.round(volumeRate(item.quantity) * 100)}% volumen</p>` : ''}
                    </div>
                </div>
            `;
            cartItems.appendChild(row);
        });

        const t = totals();
        document.getElementById('summary-subtotal').textContent = money(t.subtotal);
        document.getElementById('summary-volume').textContent = '- ' + money(t.volumeDiscount);
        document.getElementById('summary-igv').textContent = money(t.igv);
        document.getElementById('summary-total').textContent = money(t.total);
        discountInput.value = t.extra.toFixed(2);
        shippingInput.value = t.shipping.toFixed(2);
    }

    function addProduct(id) {
        const product = productMap.get(String(id));
        if (!product) return;
        if (product.stock <= 0) {
            alert('Este producto no tiene stock disponible.');
            return;
        }

        const existing = cart.get(String(id));
        const nextQuantity = (existing?.quantity || 0) + 1;

        if (nextQuantity > product.stock) {
            alert(`Stock insuficiente. Disponible: ${product.stock}.`);
            return;
        }

        cart.set(String(id), { ...product, quantity: nextQuantity });
        render();
    }

    function changeQuantity(id, delta) {
        const key = String(id);
        const item = cart.get(key);
        if (!item) return;

        const next = item.quantity + delta;

        if (next <= 0) {
            cart.delete(key);
        } else if (next > item.stock) {
            alert(`Stock insuficiente. Disponible: ${item.stock}.`);
            return;
        } else {
            item.quantity = next;
        }

        render();
    }

    productGrid.addEventListener('click', (event) => {
        const card = event.target.closest('.product-card');
        if (card) addProduct(card.dataset.id);
    });

    cartItems.addEventListener('click', (event) => {
        const inc = event.target.closest('[data-inc]');
        const dec = event.target.closest('[data-dec]');
        const remove = event.target.closest('[data-remove]');

        if (inc) changeQuantity(inc.dataset.inc, 1);
        if (dec) changeQuantity(dec.dataset.dec, -1);
        if (remove) {
            cart.delete(String(remove.dataset.remove));
            render();
        }
    });

    document.getElementById('clear-cart').addEventListener('click', () => {
        cart.clear();
        render();
    });

    clientSelect.addEventListener('change', () => {
        const option = clientSelect.selectedOptions[0];
        document.getElementById('client-hint').textContent = option?.dataset.type === 'mayorista'
            ? 'Cliente mayorista: se aplican descuentos por volumen (5% desde 12, 10% desde 50, 15% desde 100 unidades).'
            : 'Los clientes minoristas no reciben descuento automático por volumen.';
        render();
    });

    discountField.addEventListener('input', render);
    shippingField.addEventListener('input', render);

    document.getElementById('product-search').addEventListener('input', filterProducts);

    document.querySelectorAll('#category-filters [data-category]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#category-filters [data-category]').forEach((item) => {
                item.className = 'rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-200';
            });
            button.className = 'rounded-full bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white';
            filterProducts();
        });
    });

    function filterProducts() {
        const term = document.getElementById('product-search').value.toLowerCase().trim();
        const activeCategory = document.querySelector('#category-filters [data-category].bg-primary-600')?.dataset.category || 'all';
        let visible = 0;

        document.querySelectorAll('.product-card').forEach((card) => {
            const matchesTerm = term === '' || card.dataset.search.includes(term);
            const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            const show = matchesTerm && matchesCategory;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        emptyProducts.classList.toggle('hidden', visible > 0);
    }

    document.getElementById('refresh-stock').addEventListener('click', async () => {
        try {
            const results = await Promise.all([...cart.keys()].map(async (id) => {
                const response = await fetch('{{ url('/productos') }}/' + id + '/stock', { headers: { Accept: 'application/json' } });
                return response.ok ? response.json() : null;
            }));

            results.filter(Boolean).forEach((data) => {
                const product = productMap.get(String(data.id));
                if (product) product.stock = data.stock;

                const item = cart.get(String(data.id));
                if (item) item.stock = data.stock;
            });

            [...cart.entries()].forEach(([id, item]) => {
                if (item.quantity > item.stock) {
                    if (item.stock <= 0) {
                        cart.delete(id);
                    } else {
                        item.quantity = item.stock;
                    }
                }
            });

            render();
        } catch (error) {
            console.error(error);
        }
    });

    document.getElementById('pos-form').addEventListener('submit', (event) => {
        if (!clientSelect.value) {
            event.preventDefault();
            alert('Selecciona un cliente antes de registrar la venta.');
            return;
        }

        if (cart.size === 0) {
            event.preventDefault();
            alert('Agrega al menos un producto al carrito.');
            return;
        }

        itemsInput.value = JSON.stringify([...cart.values()].map((item) => ({ product_id: item.id, quantity: item.quantity })));
    });

    const clientModal = document.getElementById('client-modal');
    document.getElementById('open-client-modal').addEventListener('click', () => clientModal.classList.remove('hidden'));
    document.querySelectorAll('[data-close-client-modal]').forEach((button) => {
        button.addEventListener('click', () => clientModal.classList.add('hidden'));
    });

    render();
</script>
@endpush
