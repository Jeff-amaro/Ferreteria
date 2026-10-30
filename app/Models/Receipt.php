<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'payment_id',
        'type',
        'series',
        'number',
        'code',
        'total',
        'issued_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'factura' ? 'Factura electrónica' : 'Boleta de venta';
    }
}
