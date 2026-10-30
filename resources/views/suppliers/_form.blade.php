<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="ruc" label="RUC" :value="$supplier?->ruc" required maxlength="11" inputmode="numeric" help="11 dígitos." />
    <x-input name="business_name" label="Razón social" :value="$supplier?->business_name" required />
    <x-input name="contact_name" label="Persona de contacto" :value="$supplier?->contact_name" />
    <x-input name="phone" label="Teléfono" :value="$supplier?->phone" />
    <x-input name="email" label="Correo electrónico" type="email" :value="$supplier?->email" />
    <x-input name="lead_time_days" label="Plazo de entrega (días)" type="number" min="1" max="90" :value="$supplier?->lead_time_days ?? 7" required />
    <div class="sm:col-span-2">
        <x-input name="address" label="Dirección" :value="$supplier?->address" />
    </div>
</div>

<div class="mt-5">
    <input type="hidden" name="active" value="0">
    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(old('active', $supplier?->active ?? true))>
        <span>
            <span class="block text-sm font-semibold text-slate-800">Proveedor activo</span>
            <span class="block text-xs text-slate-500">Disponible para órdenes de compra y reposición.</span>
        </span>
    </label>
</div>
