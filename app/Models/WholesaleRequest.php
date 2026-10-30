<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WholesaleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'quotation_id',
        'requested_by',
        'evaluated_by',
        'status',
        'requested_discount',
        'approved_discount',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'requested_discount' => 'decimal:2',
        'approved_discount' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'aprobada' => 'bg-emerald-100 text-emerald-700',
            'rechazada' => 'bg-rose-100 text-rose-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }
}
