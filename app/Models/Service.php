<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    // Disable timestamps as they aren't in migration
    public $timestamps = false;

    protected $primaryKey = 'id'; 
    public $incrementing = true;

    // Defined fields from migration 
    protected $fillable = [
        'id',    
        'name', 
        'price'
    ];

    /**
     * Relationship: Service has many transaction details.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Scope: Get services by price range.
     */
    public function scopeByPriceRange(Builder $query, $minPrice, $maxPrice): Builder
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope: Get services above a certain price.
     */
    public function scopeExpensive(Builder $query, $price): Builder
    {
        return $query->where('price', '>=', $price);
    }

    /**
     * Scope: Get services below a certain price.
     */
    public function scopeAffordable(Builder $query, $price): Builder
    {
        return $query->where('price', '<', $price);
    }

    /**
     * Scope: Search services by name.
     */
    public function scopeSearchByName(Builder $query, $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Get accessor: Format price as currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}