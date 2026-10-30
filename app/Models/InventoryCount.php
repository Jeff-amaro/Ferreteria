<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'status',
        'notes',
        'counted_at',
    ];

    protected $casts = ['counted_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function getUnbalancedItemsAttribute()
    {
        return $this->items->where('balanced', false);
    }

    public function getTotalDifferenceAttribute(): int
    {
        return (int) $this->items->sum('difference');
    }

    public function getHasDifferencesAttribute(): bool
    {
        return $this->items->contains(fn ($item) => ! $item->balanced);
    }
}
