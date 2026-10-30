<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Lista de reposición</title>
    <style>
        @page { margin: 32px 36px; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
        .header { border-bottom: 3px solid #0820d7; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { font-size: 22px; font-weight: bold; color: #0820d7; margin: 0; }
        .muted { color: #64748b; }
        .supplier { margin-top: 18px; font-size: 13px; font-weight: bold; color: #3730a3; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th { background: #eef2ff; color: #3730a3; text-align: left; padding: 6px; font-size: 9px; text-transform: uppercase; }
        table.items td { padding: 6px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <p class="brand">{{ config('company.name') }}</p>
                    <p class="muted" style="margin: 2px 0;">RUC {{ config('company.ruc') }} · {{ config('company.address') }}</p>
                </td>
                <td class="right">
                    <p style="font-size: 16px; font-weight: bold; margin: 0;">LISTA DE REPOSICIÓN</p>
                    <p class="muted" style="margin: 3px 0;">{{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <p style="margin: 0;"><strong>Productos críticos:</strong> {{ $groups->flatMap(fn ($group) => $group['products'])->count() }} · <strong>Unidades sugeridas:</strong> {{ $totalUnits }}</p>

    @foreach ($groups as $group)
        <p class="supplier">{{ $group['supplier'] }}</p>
        <table class="items">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th class="center">Stock</th>
                    <th class="center">Mínimo</th>
                    <th class="center">Sugerido</th>
                    <th class="right">Costo estimado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group['products'] as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="muted">{{ $product->sku }}</td>
                        <td class="center">{{ $product->stock }}</td>
                        <td class="center">{{ $product->min_stock }}</td>
                        <td class="center"><strong>{{ $product->suggested_quantity }}</strong></td>
                        <td class="right">S/ {{ number_format((float) $product->cost * $product->suggested_quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        <p style="margin: 0;">Documento generado por {{ config('company.name') }} · {{ config('company.website') }}</p>
    </div>
</body>
</html>
