<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;

    public const STATUSES = [
        'borrador' => 'Borrador',
        'emitida' => 'Emitida',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'convertida' => 'Convertida',
    ];

    protected $fillable = [
        'code',
        'client_id',
        'user_id',
        'subtotal',
        'discount',
        'shipping',
        'igv',
        'total',
        'status',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function salesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class);
    }

    public function wholesaleRequests(): HasMany
    {
        return $this->hasMany(WholesaleRequest::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'emitida' => 'bg-sky-100 text-sky-700',
            'aceptada', 'convertida' => 'bg-emerald-100 text-emerald-700',
            'rechazada' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getIsConvertedAttribute(): bool
    {
        return $this->status === 'convertida' || $this->salesOrder()->exists();
    }
}
