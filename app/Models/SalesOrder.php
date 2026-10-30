<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pendiente' => 'Pendiente',
        'en_preparacion' => 'En preparación',
        'despachado' => 'Despachado',
        'entregado' => 'Entregado',
        'anulado' => 'Anulado',
    ];

    public const PAYMENT_STATUSES = [
        'pendiente' => 'Pendiente',
        'parcial' => 'Parcial',
        'pagado' => 'Pagado',
    ];

    public const TRANSITIONS = [
        'pendiente' => ['en_preparacion', 'anulado'],
        'en_preparacion' => ['despachado', 'anulado'],
        'despachado' => ['entregado'],
        'entregado' => [],
        'anulado' => [],
    ];

    protected $fillable = [
        'code',
        'client_id',
        'user_id',
        'quotation_id',
        'subtotal',
        'discount',
        'shipping',
        'igv',
        'total',
        'status',
        'payment_status',
        'delivery_address',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function deliveryConfirmation(): HasOne
    {
        return $this->hasOne(DeliveryConfirmation::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['entregado', 'anulado']);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendiente' => 'bg-amber-100 text-amber-700',
            'en_preparacion' => 'bg-sky-100 text-sky-700',
            'despachado' => 'bg-indigo-100 text-indigo-700',
            'entregado' => 'bg-emerald-100 text-emerald-700',
            'anulado' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'pagado' => 'bg-emerald-100 text-emerald-700',
            'parcial' => 'bg-amber-100 text-amber-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getNextStatusesAttribute(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, $this->getNextStatusesAttribute(), true);
    }

    public function getBalanceAttribute(): float
    {
        $paid = (float) $this->payments()->sum('amount');

        return max(0, (float) $this->total - $paid);
    }
}
