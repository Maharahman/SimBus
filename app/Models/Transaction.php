<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'date',
        'total_amount',
        'modified_by',
    ];

    /**
     * Relationship: A transaction is recorded by a User (Crew).
     */
    public function crew(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * Relationship: A transaction has many specific line items.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Scope: Get transactions by user.
     */
    public function scopeByUser(Builder $query, User $user): Builder
    {
        return $query->where('modified_by', $user->id);
    }

    /**
     * Scope: Get transactions within date range.
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope: Get transactions by amount range.
     */
    public function scopeByAmountRange(Builder $query, $minAmount, $maxAmount): Builder
    {
        return $query->whereBetween('total_amount', [$minAmount, $maxAmount]);
    }

    /**
     * Scope: Latest transactions first.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('date');
    }
}