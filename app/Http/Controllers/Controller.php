<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get the currently authenticated user.
     */
    protected function currentUser()
    {
        return Auth::user();
    }

    /**
     * Check if user is developer.
     */
    protected function isDeveloper(): bool
    {
        return Auth::check() && Auth::user()->level === 'developer';
    }

    /**
     * Check if user is admin or above.
     */
    protected function isAdmin(): bool
    {
        return Auth::check() && in_array(Auth::user()->level, ['admin', 'developer']);
    }

    /**
     * Abort with 403 Forbidden for unauthorized actions.
     */
    protected function unauthorized($message = 'Unauthorized action.')
    {
        return redirect()->back()->with('error', $message);
    }

    /**
     * Check if user is developer, else return unauthorized response.
     */
    protected function requireDeveloper()
    {
        if (!$this->isDeveloper()) {
            return $this->unauthorized('This action requires developer privileges.');
        }
        return true;
    }

    /**
     * Check if user is admin, else return unauthorized response.
     */
    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            return $this->unauthorized('This action requires admin privileges.');
        }
        return true;
    }
}
