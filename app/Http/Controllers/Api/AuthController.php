<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
        // 1. Validate the incoming request from Android
        $fields = $request->validate([
            'num' => 'required|string',
            'pass' => 'required|string'
        ]);

        // 2. Find the user by their custom 'num' field
        $user = User::where('num', $fields['num'])->first();

        // 3. Check the password (using Hash::check if you hashed them)
        if (!$user || !Hash::check($fields['pass'], $user->pass)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        // 4. Create the Sanctum token for the Android device
        $token = $user->createToken('barber_app_token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }
}