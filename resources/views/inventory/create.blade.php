@extends('layouts.app')

@section('title', 'Nuevo conteo físico')

@section('content')
    <x-page-header title="Nuevo conteo físico" subtitle="Ingresa la cantidad real contada para cada producto. Las diferencias generarán una auditoría.">
        <a href="{{ route('inventory.index') }}" class="btn-ghost">Volver</a>
    </x-page-header>

    <form method="POST" action="{{ route('inventory.store') }}" id="count-form" class="space-y-4">
        @csrf

        <div class="card p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" id="count-search" placeholder="Buscar producto por nombre o SKU..." class="input pl-9">
                </div>
                <button type="button" id="reset-count" class="btn-secondary"><x-icon name="refresh" class="h-4 w-4" /> Usar stock del sistema</button>
            </div>
            <div class="mt-3">
                <label class="label" for="notes">Observaciones del conteo</label>
                <textarea name="notes" id="notes" rows="2" class="input" placeholder="Responsable del conteo, turno, observaciones...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-base" id="count-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock sistema</th>
                            <th class="w-40 text-center">Stock físico</th>
                            <th class="text-center">Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr data-search="{{ \Illuminate\Support\Str::lower($product->name.' '.$product->sku) }}">
                                <td>
                                    <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                                </td>
                                <td class="text-sm text-slate-600">{{ $product->category?->name ?? 'General' }}</td>
                                <td class="text-center font-semibold text-slate-700">{{ $product->stock }} {{ $product->unit }}</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="items[{{ $product->id }}]"
                                        value="{{ old('items.'.$product->id, $product->stock) }}"
                                        min="0"
                                        step="1"
                                        data-system="{{ $product->stock }}"
                                        class="input physical-input w-24 text-center"
                                        required>
                                </td>
                                <td class="text-center">
                                    <span class="difference-label font-semibold text-slate-400">0</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="difference-summary">Sin diferencias registradas.</p>
                <button type="submit" class="btn-primary"><x-icon name="check" class="h-4 w-4" /> Guardar conteo</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.physical-input');

    function updateDifferences() {
        let differences = 0;

        inputs.forEach((input) => {
            const system = Number(input.dataset.system) || 0;
            const physical = Number(input.value);
            const label = input.closest('tr').querySelector('.difference-label');

            if (input.value === '' || Number.isNaN(physical)) {
                label.textContent = '—';
                label.className = 'difference-label font-semibold text-slate-400';
                return;
            }

            const difference = physical - system;
            label.textContent = difference > 0 ? '+' + difference : String(difference);
            label.className = 'difference-label font-semibold ' + (difference === 0 ? 'text-slate-400' : difference > 0 ? 'text-emerald-600' : 'text-rose-600');

            if (difference !== 0) differences++;
        });

        document.getElementById('difference-summary').textContent = differences === 0
            ? 'Sin diferencias registradas.'
            : differences + ' productos presentan diferencias. Se generará una auditoría automáticamente.';
    }

    inputs.forEach((input) => input.addEventListener('input', updateDifferences));

    document.getElementById('reset-count').addEventListener('click', () => {
        inputs.forEach((input) => {
            input.value = input.dataset.system;
        });
        updateDifferences();
    });

    document.getElementById('count-search').addEventListener('input', (event) => {
        const term = event.target.value.toLowerCase().trim();
        document.querySelectorAll('#count-table tbody tr').forEach((row) => {
            row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
        });
    });

    updateDifferences();
</script>
@endpush
