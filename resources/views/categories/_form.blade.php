<div class="space-y-5">
    <x-input name="name" label="Nombre de la categoría" :value="$category?->name" required placeholder="Herramientas manuales" />
    <div>
        <label for="description" class="label">Descripción</label>
        <textarea name="description" id="description" rows="4" class="input" placeholder="Describe qué productos agrupa esta categoría…">{{ old('description', $category?->description) }}</textarea>
        @error('description')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <input type="hidden" name="active" value="0">
        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(old('active', $category?->active ?? true))>
            <span>
                <span class="block text-sm font-semibold text-slate-800">Categoría activa</span>
                <span class="block text-xs text-slate-500">Visible en filtros y en el catálogo público.</span>
            </span>
        </label>
    </div>
</div>
