<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'alertable_type',
        'alertable_id',
        'status',
    ];

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pendiente');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'desbalance' => 'Desbalance de inventario',
            'retraso' => 'Retraso de proveedor',
            'stock_bajo' => 'Stock bajo',
            default => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'desbalance' => 'bg-rose-100 text-rose-700',
            'retraso' => 'bg-amber-100 text-amber-700',
            default => 'bg-sky-100 text-sky-700',
        };
    }
}
