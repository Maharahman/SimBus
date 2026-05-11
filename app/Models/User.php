<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens;

    // 1. Tell Laravel you are using 'id' as the ID
    protected $primaryKey = 'id'; 
    
    // 2. Tell Laravel your table DOES NOT have created_at / updated_at
    public $timestamps = false; 

    protected $fillable = ['name', 'sur_name', 'num', 'pass', 'level', 'profile_photo'];

    protected $hidden = ['pass'];

    /**
     * Scope: Get only developer level users.
     */
    public function scopeDevelopers(Builder $query)
    {
        return $query->where('level', 'developer');
    }

    /**
     * Scope: Get only admin level users.
     */
    public function scopeAdmins(Builder $query)
    {
        return $query->where('level', 'admin');
    }

    /**
     * Scope: Get only regular users.
     */
    public function scopeRegularUsers(Builder $query)
    {
        return $query->where('level', 'user');
    }

    /**
     * Scope: Exclude a user.
     */
    public function scopeExcluding(Builder $query, $userId)
    {
        return $query->where('id', '!=', $userId);
    }

    /**
     * Scope: Get users visible to the current user level.
     */
    public function scopeVisibleTo(Builder $query, User $user)
    {
        if ($user->level === 'developer') {
            return $query;
        }

        return $query->where('level', '!=', 'developer');
    }

    /**
     * Relationship: User has many tickets.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Relationship: User can handle multiple transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'modified_by');
    }
}