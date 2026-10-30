<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $receipt->code }}</title>
    <style>
        @page { margin: 36px 40px; }
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
        .totals { width: 250px; margin-left: auto; margin-top: 14px; }
        .totals td { padding: 4px 0; }
        .total-final { border-top: 2px solid #0820d7; font-size: 15px; font-weight: bold; color: #0820d7; padding-top: 8px !important; }
        .footer { margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 10px; color: #64748b; text-align: center; }
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
                    <p style="font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 0;">Comprobante de pago</p>
                    <p style="font-size: 18px; font-weight: bold; margin: 4px 0;">{{ $receipt->type_label }}</p>
                    <p style="font-size: 16px; font-weight: bold; color: #0820d7; margin: 0;">{{ $receipt->code }}</p>
                    <p class="muted" style="margin: 4px 0 0;">Emitido: {{ $receipt->issued_at?->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="row">
        <tr>
            <td style="width: 55%; padding-right: 12px;">
                <div class="box">
                    <p class="box-title">Cliente</p>
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $receipt->salesOrder?->client?->name }}</p>
                    <p style="margin: 3px 0;" class="muted">{{ $receipt->salesOrder?->client?->document_label }}</p>
                    <p style="margin: 3px 0;" class="muted">{{ $receipt->salesOrder?->client?->address ?? 'Sin dirección registrada' }}</p>
                </div>
            </td>
            <td style="width: 45%;">
                <div class="box">
                    <p class="box-title">Datos del pago</p>
                    <p style="margin: 3px 0;">Orden: {{ $receipt->salesOrder?->code }}</p>
                    <p style="margin: 3px 0;">Método: {{ $receipt->payment?->method_label }}</p>
                    <p style="margin: 3px 0;">Operación: {{ $receipt->payment?->reference ?? '—' }}</p>
                    <p style="margin: 3px 0;">Cajero: {{ $receipt->payment?->user?->name }}</p>
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
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($receipt->salesOrder?->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="muted">{{ $item->sku }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">S/ {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="right">S/ {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">S/ {{ number_format((float) $receipt->salesOrder?->subtotal, 2) }}</td></tr>
        <tr><td class="muted">Descuento</td><td class="right">- S/ {{ number_format((float) $receipt->salesOrder?->discount, 2) }}</td></tr>
        <tr><td class="muted">Envío</td><td class="right">S/ {{ number_format((float) $receipt->salesOrder?->shipping, 2) }}</td></tr>
        <tr><td class="muted">IGV (18%)</td><td class="right">S/ {{ number_format((float) $receipt->salesOrder?->igv, 2) }}</td></tr>
        <tr><td class="total-final">Total</td><td class="right total-final">S/ {{ number_format((float) $receipt->total, 2) }}</td></tr>
        <tr><td class="muted">Monto recibido</td><td class="right">S/ {{ number_format((float) $receipt->payment?->amount, 2) }}</td></tr>
        <tr><td class="muted">Vuelto</td><td class="right">S/ {{ number_format((float) $receipt->payment?->change_amount, 2) }}</td></tr>
    </table>

    <div class="footer">
        <p style="margin: 0;">Gracias por su compra. Comprobante generado electrónicamente por {{ config('company.name') }}.</p>
        <p style="margin: 4px 0 0;">{{ config('company.website') }}</p>
    </div>
</body>
</html>
