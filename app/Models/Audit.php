<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_count_id',
        'code',
        'reason',
        'status',
        'requested_by',
        'resolved_by',
        'notes',
        'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function inventoryCount(): BelongsTo
    {
        return $this->belongsTo(InventoryCount::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'resuelta' => 'bg-emerald-100 text-emerald-700',
            'en_proceso' => 'bg-sky-100 text-sky-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }
}
