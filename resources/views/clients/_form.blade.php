<div class="grid gap-5 sm:grid-cols-2">
    <x-select name="document_type" label="Tipo de documento" :options="['DNI' => 'DNI', 'RUC' => 'RUC']" :selected="$client?->document_type ?? 'DNI'" required />
    <x-input name="document_number" label="Número de documento" :value="$client?->document_number" required maxlength="11" inputmode="numeric" help="8 dígitos para DNI, 11 para RUC." />
    <x-input name="name" label="Nombre o razón social" :value="$client?->name" required />
    <x-select name="type" label="Tipo de cliente" :options="['minorista' => 'Minorista', 'mayorista' => 'Mayorista']" :selected="$client?->type ?? 'minorista'" required />
    <x-input name="email" label="Correo electrónico" type="email" :value="$client?->email" />
    <x-input name="phone" label="Teléfono" :value="$client?->phone" />
    <div class="sm:col-span-2">
        <x-input name="address" label="Dirección" :value="$client?->address" />
    </div>
</div>

<div class="mt-5">
    <input type="hidden" name="active" value="0">
    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(old('active', $client?->active ?? true))>
        <span>
            <span class="block text-sm font-semibold text-slate-800">Cliente activo</span>
            <span class="block text-xs text-slate-500">Disponible para cotizaciones y órdenes de venta.</span>
        </span>
    </label>
</div>
