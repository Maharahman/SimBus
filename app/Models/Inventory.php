<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = ['item_name', 'item_price', 'stock', 'last_updated_by'];

    /**
     * Relationship: Inventory item was last edited by a User.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Relationship: Inventory item appears in many transaction details.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Scope: Get low stock items.
     */
    public function scopeLowStock(Builder $query, $threshold = 10): Builder
    {
        return $query->where('stock', '<', $threshold);
    }

    /**
     * Scope: Get out of stock items.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope: Get items by price range.
     */
    public function scopeByPriceRange(Builder $query, $minPrice, $maxPrice): Builder
    {
        return $query->whereBetween('item_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope: Search items by name.
     */
    public function scopeSearchByName(Builder $query, $name): Builder
    {
        return $query->where('item_name', 'like', "%{$name}%");
    }

    /**
     * Scope: Get recently updated items.
     */
    public function scopeRecentlyUpdated(Builder $query, $days = 7): Builder
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    /**
     * Get accessor: Format price as currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->item_price, 0, ',', '.');
    }

    /**
     * Determine if item is low stock.
     */
    public function isLowStock($threshold = 10): bool
    {
        return $this->stock < $threshold;
    }

    /**
     * Determine if item is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }
}