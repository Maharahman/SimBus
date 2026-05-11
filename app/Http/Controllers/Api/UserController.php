<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * GET /api/users
     * List all users for the management table.
     */
    public function index()
    {
        $users = User::all(['id', 'name', 'sur_name', 'num', 'level']);
        return response()->json($users, 200);
    }

    /**
     * POST /api/users
     * Feature: Add another user (only adding admin and staff).
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $auth */
        $auth = Auth::user();

        // 1. Only Developer can add users
        if ($auth->level !== 'developer') {
            return response()->json(['message' => 'Forbidden: Developer access required'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'sur_name' => 'required|string|max:255',
            'num'      => 'required|numeric|unique:users,num',
            'pass'     => 'required|min:6',
            'level'    => 'required|in:admin,staff' // STRICT: Cannot add 'developer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'sur_name' => $request->sur_name,
            'num'      => $request->num,
            'pass'     => Hash::make($request->pass),
            'level'    => $request->level,
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    /**
     * PUT /api/users/{id}
     * Feature: Edit all users below developer level (num and pass included).
     */
    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $auth */
        $auth = Auth::user();
        $user = User::findOrFail($id);

        // 1. Permission: Developer can edit anyone. Others can ONLY edit themselves.
        if ($auth->level !== 'developer' && $auth->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to edit this user'], 403);
        }

        // 2. Validation: 'num' must be unique EXCEPT for the current user being edited
        $validator = Validator::make($request->all(), [
            'num'   => 'numeric|unique:users,num,' . $id,
            'level' => 'in:admin,staff,developer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 3. Update Fields
        $user->name = $request->name ?? $user->name;
        $user->sur_name = $request->sur_name ?? $user->sur_name;
        $user->num = $request->num ?? $user->num;
        
        // Only a Developer can promote/demote levels
        if ($auth->level === 'developer') {
            $user->level = $request->level ?? $user->level;
        }

        // Feature: Update password if provided
        if ($request->filled('pass')) {
            $user->pass = Hash::make($request->pass);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
    }

    /**
     * DELETE /api/users/{id}
     * Feature: Delete users below developer level.
     */
    public function destroy($id)
    {
        /** @var \App\Models\User $auth */
        $auth = Auth::user();
        $user = User::findOrFail($id);

        // 1. Only Developer can delete
        if ($auth->level !== 'developer') {
            return response()->json(['message' => 'Forbidden: Developer access required'], 403);
        }

        // 2. Protection: Cannot delete other developers or yourself
        if ($user->level === 'developer') {
            return response()->json(['message' => 'System Protection: Cannot delete developer accounts'], 422);
        }

        // This call is now IDE-safe because of findOrFail + @var hint
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}