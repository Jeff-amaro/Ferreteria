@php
    $categoryOptions = $categories->pluck('name', 'id')->all();
    $brandOptions = $brands->pluck('name', 'id')->all();
    $supplierOptions = $suppliers->pluck('business_name', 'id')->all();
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="sku" label="SKU" :value="$product?->sku" required placeholder="HERR-001" />
    <x-input name="name" label="Nombre del producto" :value="$product?->name" required placeholder="Martillo de uña 16 oz" />
    <x-select name="category_id" label="Categoría" :options="$categoryOptions" :selected="$product?->category_id" placeholder="Sin categoría" />
    <x-select name="brand_id" label="Marca" :options="$brandOptions" :selected="$product?->brand_id" placeholder="Sin marca" />
    <x-select name="supplier_id" label="Proveedor" :options="$supplierOptions" :selected="$product?->supplier_id" placeholder="Sin proveedor" />
    <x-input name="unit" label="Unidad de medida" :value="$product?->unit ?? 'unidad'" required placeholder="unidad, caja, galón…" />
    <x-input name="price" label="Precio de venta (S/)" type="number" step="0.01" min="0.01" :value="$product?->price" required />
    <x-input name="cost" label="Costo (S/)" type="number" step="0.01" min="0" :value="$product?->cost ?? 0" />
    <x-input name="stock" label="Stock inicial" type="number" min="0" :value="$product?->stock ?? 0" required />
    <x-input name="min_stock" label="Stock mínimo" type="number" min="0" :value="$product?->min_stock ?? 5" required help="Se usa para alertas y reposición." />
</div>

<div class="mt-5">
    <label for="description" class="label">Descripción</label>
    <textarea name="description" id="description" rows="3" class="input" placeholder="Descripción comercial del producto…">{{ old('description', $product?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="mt-5">
    <label for="specs" class="label">Especificaciones técnicas</label>
    <textarea name="specs" id="specs" rows="3" class="input" placeholder="Medidas, material, potencia, normas…">{{ old('specs', $product?->specs) }}</textarea>
    @error('specs')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="mt-5 grid gap-5 sm:grid-cols-2">
    <div>
        <label for="image" class="label">Fotografía del producto</label>
        <input type="file" name="image" id="image" accept="image/*" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700">
        <p class="mt-1 text-xs text-slate-500">JPG, PNG o WebP. Máximo 2 MB.</p>
        @error('image')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-end">
        @if ($product?->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-24 w-24 rounded-xl border border-slate-200 object-cover">
        @else
            <div class="flex h-24 w-24 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-400">
                <x-icon name="box" class="h-8 w-8" />
            </div>
        @endif
    </div>
</div>

<div class="mt-5">
    <input type="hidden" name="active" value="0">
    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(old('active', $product?->active ?? true))>
        <span>
            <span class="block text-sm font-semibold text-slate-800">Producto activo</span>
            <span class="block text-xs text-slate-500">Visible en el catálogo y disponible para la venta.</span>
        </span>
    </label>
</div>
