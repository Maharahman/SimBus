<?php
namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'date',
        'total_amount',
        'modified_by', // This stores the ID of the User
    ];

    /**
     * Relationship: A transaction is recorded by a User (Crew).
     */
    public function crew(): BelongsTo
    {
        // We point this to the User model since that's what you're using
        return $this->belongsTo(User::class, 'modified_by');
    }

    /**
     * Relationship: A transaction has many specific line items.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}