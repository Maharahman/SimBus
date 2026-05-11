<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'item_type', // 'service' or 'inventory'
        'item_id',
        'quantity',
        'price_at_time',
    ];

    /**
     * Logic Hook: Handle automatic stock changes.
     */
    protected static function booted()
    {
        // Subtract stock when a product is sold
        static::created(function ($detail) {
            if (in_array($detail->item_type, ['inventory', 'item'])) {
                $detail->inventoryItem()->decrement('stock', $detail->quantity);
            }
        });

        // Restore stock when a receipt is deleted
        static::deleting(function ($detail) {
            if (in_array($detail->item_type, ['inventory', 'item'])) {
                // Returns the items to the shelf automatically
                $detail->inventoryItem()->increment('stock', $detail->quantity);
            }
        });
    }

    /**
     * Relationship: Link to the Inventory model.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'item_id');
    }

    /**
     * Relationship: Link to the Service model.
     */
    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'item_id');
    }

    /**
     * Relationship: Back to the parent transaction.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}