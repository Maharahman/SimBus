<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Handle the Web Login Request
     */
    public function webLogin(Request $request)
    {
        // 1. Validate the incoming request
        $credentials = $request->validate([
            'num'  => 'required|string',
            'pass' => 'required|string',
        ]);
        // 2. Look for the user in the 'sim_bus.users' table
        // We use 'num' because that is your primary identifier
        $user = User::where('num', $request->num)->first();
        // 3. Verify the user exists and the password matches
        // Hash::check compares the plain text input with the Bcrypt hash in DB
        if ($user && Hash::check($request->pass, $user->pass)) {
            // Log the user into the session
            Auth::login($user);
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();
            // Redirect to the dashboard (we will create this route next)
            return redirect()->intended('admin/dashboard');
        }

        // 4. If authentication fails, redirect back with an error message
        return back()->withErrors([
            'num' => 'The provided credentials do not match our records.',
        ])->onlyInput('num');
    }

    /**
     * Handle Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}