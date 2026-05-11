<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display all users visible to the current user.
     */
    public function index()
    {
        $currentUser = $this->currentUser();
        $users = User::visibleTo($currentUser)->excluding($currentUser->id)->get();

        return view('index.children_views.user', compact('users'));
    }

    /**
     * Store a new user (Developer only).
     */
    public function store(StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());

        return redirect()->back()->with('success', 'User created successfully!');
    }

    /**
     * Update user information.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        
        $data = $request->validated();
        
        // Only developers can change levels
        if ($this->currentUser()->level !== 'developer') {
            unset($data['level']);
        }

        $this->userService->updateUser($user, $data);

        return redirect()->back()->with('success', 'User updated!');
    }

    /**
     * Delete a user (Developer only).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $currentUser = $this->currentUser();

        if (!$this->userService->canDelete($currentUser, $user)) {
            return $this->unauthorized('Cannot delete this user.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted.');
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $this->currentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'sur_name' => 'sometimes|required|string|max:255',
            'num' => 'sometimes|required|numeric|unique:users,num,' . $user->id,
            'pass' => 'sometimes|nullable|min:6',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'sur_name', 'num', 'pass']);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }
            
            $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $this->userService->updateUser($user, array_filter($data));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}