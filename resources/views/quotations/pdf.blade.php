<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cotización {{ $quotation->code }}</title>
    <style>
        @page { margin: 32px 36px; }
        * { box-sizing: border-box; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #1e293b; margin: 0; }
        .header { border-bottom: 3px solid #0820d7; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 24px; font-weight: bold; color: #0820d7; margin: 0; }
        .muted { color: #64748b; }
        .row { width: 100%; }
        .row td { vertical-align: top; }
        .box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; }
        .box-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 4px; font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.items th { background: #eef2ff; color: #3730a3; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; }
        table.items td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals { width: 240px; margin-left: auto; margin-top: 14px; }
        .totals td { padding: 4px 0; }
        .total-final { border-top: 2px solid #0820d7; font-size: 15px; font-weight: bold; color: #0820d7; padding-top: 8px !important; }
        .footer { margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 10px; color: #64748b; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; background: #eef2ff; color: #3730a3; font-size: 10px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <table class="row">
            <tr>
                <td>
                    <p class="brand">{{ config('company.name') }}</p>
                    <p class="muted" style="margin: 2px 0;">RUC {{ config('company.ruc') }}</p>
                    <p class="muted" style="margin: 2px 0;">{{ config('company.address') }}</p>
                    <p class="muted" style="margin: 2px 0;">{{ config('company.phone') }} · {{ config('company.email') }}</p>
                </td>
                <td class="right">
                    <p style="font-size: 18px; font-weight: bold; margin: 0;">COTIZACIÓN</p>
                    <p style="font-size: 16px; font-weight: bold; color: #0820d7; margin: 4px 0;">{{ $quotation->code }}</p>
                    <span class="badge">{{ $quotation->status_label }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="row">
        <tr>
            <td style="width: 60%; padding-right: 12px;">
                <div class="box">
                    <p class="box-title">Cliente</p>
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $quotation->client?->name }}</p>
                    <p style="margin: 3px 0;" class="muted">{{ $quotation->client?->document_label }} · {{ $quotation->client?->type_label }}</p>
                    <p style="margin: 3px 0;" class="muted">{{ $quotation->client?->address ?? 'Sin dirección registrada' }}</p>
                    <p style="margin: 3px 0;" class="muted">{{ $quotation->client?->phone ?? 'Sin teléfono' }} · {{ $quotation->client?->email ?? 'Sin correo' }}</p>
                </div>
            </td>
            <td style="width: 40%;">
                <div class="box">
                    <p class="box-title">Datos de la cotización</p>
                    <p style="margin: 3px 0;">Emisión: {{ $quotation->created_at->format('d/m/Y H:i') }}</p>
                    <p style="margin: 3px 0;">Vigencia: {{ $quotation->valid_until?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                    <p style="margin: 3px 0;">Vendedor: {{ $quotation->user?->name }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>SKU</th>
                <th class="center">Cant.</th>
                <th class="right">Precio</th>
                <th class="right">Descuento</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="muted">{{ $item->sku }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="right">- S/ {{ number_format((float) $item->discount, 2) }}</td>
                    <td class="right">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">S/ {{ number_format((float) $quotation->subtotal, 2) }}</td></tr>
        <tr><td class="muted">Descuento</td><td class="right">- S/ {{ number_format((float) $quotation->discount, 2) }}</td></tr>
        <tr><td class="muted">Envío</td><td class="right">S/ {{ number_format((float) $quotation->shipping, 2) }}</td></tr>
        <tr><td class="muted">IGV (18%)</td><td class="right">S/ {{ number_format((float) $quotation->igv, 2) }}</td></tr>
        <tr><td class="total-final">Total</td><td class="right total-final">S/ {{ number_format((float) $quotation->total, 2) }}</td></tr>
    </table>

    @if ($quotation->notes)
        <div style="margin-top: 18px;">
            <p class="box-title">Observaciones</p>
            <p style="margin: 0;">{{ $quotation->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p style="margin: 0;">Cotización válida hasta el {{ $quotation->valid_until?->format('d/m/Y') ?? '—' }}. Los precios incluyen IGV según la normativa vigente.</p>
        <p style="margin: 4px 0 0;">{{ config('company.name') }} · {{ config('company.website') }} · Gracias por su preferencia.</p>
    </div>
</body>
</html>
