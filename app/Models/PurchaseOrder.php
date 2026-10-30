<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pendiente' => 'Pendiente',
        'en_transito' => 'En tránsito',
        'recibida' => 'Recibida',
        'retrasada' => 'Retrasada',
    ];

    protected $fillable = [
        'code',
        'supplier_id',
        'user_id',
        'status',
        'ordered_at',
        'expected_at',
        'received_at',
        'total',
        'notes',
    ];

    protected $casts = [
        'ordered_at' => 'date',
        'expected_at' => 'date',
        'received_at' => 'date',
        'total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'en_transito' => 'bg-sky-100 text-sky-700',
            'recibida' => 'bg-emerald-100 text-emerald-700',
            'retrasada' => 'bg-rose-100 text-rose-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    public function getIsDelayedAttribute(): bool
    {
        return ! in_array($this->status, ['recibida'], true)
            && $this->expected_at
            && $this->expected_at->isPast();
    }

    public function getDaysLateAttribute(): int
    {
        return $this->is_delayed ? (int) $this->expected_at->diffInDays(now()) : 0;
    }
}
