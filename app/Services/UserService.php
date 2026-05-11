<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user with hashed password.
     */
    public function createUser(array $data): User
    {
        $data['pass'] = Hash::make($data['pass']);
        return User::create($data);
    }

    /**
     * Update user data.
     */
    public function updateUser(User $user, array $data): User
    {
        if (isset($data['pass'])) {
            $data['pass'] = Hash::make($data['pass']);
        } else {
            unset($data['pass']);
        }
        
        $user->update($data);
        return $user;
    }

    /**
     * Get users filtered by level and excludes.
     */
    public function getUsersForLevel(User $currentUser, array $excludeIds = []): \Illuminate\Database\Eloquent\Collection
    {
        if ($currentUser->level === 'developer') {
            return User::whereNotIn('id', array_merge([$currentUser->id], $excludeIds))->get();
        }

        return User::where('level', '!=', 'developer')
            ->whereNotIn('id', array_merge([$currentUser->id], $excludeIds))
            ->get();
    }

    /**
     * Determine if user can be deleted.
     */
    public function canDelete(User $currentUser, User $targetUser): bool
    {
        return $currentUser->level === 'developer' && $targetUser->level !== 'developer';
    }
}
