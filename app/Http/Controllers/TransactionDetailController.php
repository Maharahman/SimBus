<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'item_type',    // 'service' or 'inventory'
        'item_id',      // ID from respective table
        'quantity',
        'price_at_time' // Snapshotted price (so history doesn't change if you raise prices later)
    ];

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'item_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'item_id');
    }

    protected static function booted()
    {
        // When a receipt line is created, if it's an item, reduce stock
        static::created(function ($detail) {
            if ($detail->item_type === 'inventory') {
                $detail->inventoryItem()->decrement('stock', $detail->quantity);
            }
        });

        // When a receipt line is deleted, put stock back
        static::deleted(function ($detail) {
            if ($detail->item_type === 'inventory') {
                $detail->inventoryItem()->increment('stock', $detail->quantity);
            }
        });
    }
}