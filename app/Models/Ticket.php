<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'category',
        'subject',
        'message',
        'registration_data',
        'status',
        'handled_by',
        'user_num',
        'user_id',
        'dev_reply'
    ];

    /**
     * Relationship: Ticket belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: Get only registration tickets.
     */
    public function scopeRegistrations(Builder $query): Builder
    {
        return $query->where('category', 'registration');
    }

    /**
     * Scope: Get only application issue tickets.
     */
    public function scopeAppIssues(Builder $query): Builder
    {
        return $query->where('category', 'app_issue');
    }

    /**
     * Scope: Get open tickets.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'Open');
    }

    /**
     * Scope: Get tickets in progress.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'Progress');
    }

    /**
     * Scope: Get completed tickets (Closed, Approved, Rejected).
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereIn('status', ['Closed', 'Approve', 'Reject']);
    }

    /**
     * Scope: Get tickets for a specific user.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope: Get by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get latest tickets first.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('id');
    }
}