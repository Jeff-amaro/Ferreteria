<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'received_by',
        'document_number',
        'signature_path',
        'notes',
        'confirmed_at',
    ];

    protected $casts = ['confirmed_at' => 'datetime'];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
