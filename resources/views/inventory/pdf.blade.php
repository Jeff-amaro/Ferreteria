<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Conteo {{ $inventoryCount->code }}</title>
    <style>
        @page { margin: 32px 36px; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
        .header { border-bottom: 3px solid #0820d7; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { font-size: 22px; font-weight: bold; color: #0820d7; margin: 0; }
        .muted { color: #64748b; }
        .row { width: 100%; }
        .row td { vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th { background: #eef2ff; color: #3730a3; text-align: left; padding: 7px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        table.items td { padding: 7px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .center { text-align: center; }
        .danger { color: #be123c; font-weight: bold; }
        .success { color: #047857; font-weight: bold; }
        .footer { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <table class="row">
            <tr>
                <td>
                    <p class="brand">{{ config('company.name') }}</p>
                    <p class="muted" style="margin: 2px 0;">RUC {{ config('company.ruc') }} · {{ config('company.address') }}</p>
                </td>
                <td class="right">
                    <p style="font-size: 16px; font-weight: bold; margin: 0;">CONTEÓ FÍSICO DE INVENTARIO</p>
                    <p style="font-size: 14px; font-weight: bold; color: #0820d7; margin: 3px 0;">{{ $inventoryCount->code }}</p>
                    <p class="muted" style="margin: 0;">{{ $inventoryCount->counted_at?->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="row">
        <tr>
            <td style="width: 50%; padding-right: 12px;">
                <p style="margin: 2px 0;"><strong>Responsable:</strong> {{ $inventoryCount->user?->name ?? '—' }}</p>
                <p style="margin: 2px 0;"><strong>Estado:</strong> {{ ucfirst($inventoryCount->status) }}</p>
                <p style="margin: 2px 0;"><strong>Productos:</strong> {{ $inventoryCount->items->count() }}</p>
            </td>
            <td style="width: 50%;">
                <p style="margin: 2px 0;"><strong>Diferencias:</strong> {{ $inventoryCount->items->where('balanced', false)->count() }}</p>
                <p style="margin: 2px 0;"><strong>Diferencia total:</strong> {{ $inventoryCount->total_difference }} unidades</p>
                @if ($inventoryCount->notes)
                    <p style="margin: 2px 0;"><strong>Observaciones:</strong> {{ $inventoryCount->notes }}</p>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>SKU</th>
                <th class="center">Stock sistema</th>
                <th class="center">Stock físico</th>
                <th class="center">Diferencia</th>
                <th class="center">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryCount->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="muted">{{ $item->sku }}</td>
                    <td class="center">{{ $item->system_stock }}</td>
                    <td class="center">{{ $item->physical_stock }}</td>
                    <td class="center {{ $item->difference === 0 ? '' : 'danger' }}">{{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}</td>
                    <td class="center {{ $item->balanced ? 'success' : 'danger' }}">{{ $item->balanced ? 'Cuadrado' : 'Desbalance' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p style="margin: 0;">Documento generado por {{ config('company.name') }} · {{ config('company.website') }}</p>
    </div>
</body>
</html>
