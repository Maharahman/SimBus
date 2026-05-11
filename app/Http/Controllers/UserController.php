<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $currentUser = Auth::user();

    // Shield Developer accounts from Admins AND hide self from the list
    if ($currentUser->level !== 'developer') {
        $users = User::where('level', '!=', 'developer')
                     ->where('id', '!=', $currentUser->id) // Hide self
                     ->get();
    } else {
        // Developers see everyone EXCEPT themselves
        $users = User::where('id', '!=', $currentUser->id)->get();
    }

    return view('index.children_views.user', compact('users'));
}

    public function store(Request $request)
    {
        // Safety check to ensure Auth::user() isn't null
        $auth = Auth::user();
        if (!$auth || $auth->level !== 'developer') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string',
            'sur_name' => 'required|string',
            'num' => 'required|numeric|unique:users,num',
            'pass' => 'required|min:6',
            'level' => 'required|in:admin,user', 
        ]);

        User::create([
            'name' => $request->name,
            'sur_name' => $request->sur_name,
            'num' => $request->num,
            'pass' => Hash::make($request->pass),
            'level' => $request->level, 
        ]);

        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, $id)
    {
        $auth = Auth::user();
        $user = User::findOrFail($id);

        // Prevent non-developers from editing others
        if (!$auth || ($auth->level !== 'developer' && $auth->id !== $user->id)) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'num' => 'required|numeric|unique:users,num,' . $id,
            'level' => 'required|in:admin,user,developer'
        ]);

        $user->name = $request->name;
        $user->sur_name = $request->sur_name;
        $user->num = $request->num;

        // Only developer can change account levels
        if ($auth->level === 'developer') {
            $user->level = $request->level;
        }

        if ($request->filled('pass')) {
            $user->pass = Hash::make($request->pass);
        }

        $user->save();
        return redirect()->back()->with('success', 'User updated!');
    }

    public function destroy($id)
    {
        $auth = Auth::user();
        $user = User::findOrFail($id);

        // Deny if not developer OR trying to delete a developer
        if (!$auth || $auth->level !== 'developer' || $user->level === 'developer') {
            return redirect()->back()->with('error', 'Action denied.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $request->validate([
            'name' => 'required',
            'sur_name' => 'required',
            'num' => 'required|numeric|unique:users,num,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->name = $request->name;
        $user->sur_name = $request->sur_name;
        $user->num = $request->num;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }
            
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        if ($request->filled('pass')) {
            $user->pass = Hash::make($request->pass);
        }

        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}