<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'branch'])->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::all();
        return view('users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::requiredIf(function () use ($request) {
                    return collect($request->roles)->contains(function ($roleId) {
                        $role = Role::find($roleId);
                        return in_array($role->name, ['Branch Manager', 'Stock Manager']);
                    });
                }),
            ],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $validatedData['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        // Create user
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'branch_id' => $validatedData['branch_id'] ?? null,
            'avatar' => $validatedData['avatar'] ?? null,
        ]);

        // Sync roles
        $user->roles()->sync($validatedData['roles']);

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('users.create')
                ->with('success', 'User created successfully. Create another one.');
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'branch']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::all();
        return view('users.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => [
                'nullable',
                'exists:branches,id',
                Rule::requiredIf(function () use ($request) {
                    return collect($request->roles)->contains(function ($roleId) {
                        $role = Role::find($roleId);
                        return in_array($role->name, ['Branch Manager', 'Stock Manager']);
                    });
                }),
            ],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $validatedData['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        // Update user
        $user->update([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'branch_id' => $validatedData['branch_id'] ?? null,
            'avatar' => $validatedData['avatar'] ?? $user->avatar,
        ]);

        // Update password if provided
        if (!empty($validatedData['password'])) {
            $user->update([
                'password' => Hash::make($validatedData['password'])
            ]);
        }

        // Sync roles
        $user->roles()->sync($validatedData['roles']);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Delete avatar if exists
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        // Delete user
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function deleteAvatar(User $user)
    {
        $user->deleteAvatar();
        return redirect()->back()
            ->with('success', 'Avatar deleted successfully.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'avatar' => 'nullable|image|max:2048',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $validatedData['avatar'] = $request->file('avatar')
                ->store('avatars', 'public');
        }

        $user->update([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'avatar' => $validatedData['avatar'] ?? $user->avatar,
        ]);

        if (!empty($validatedData['new_password'])) {
            $user->update([
                'password' => Hash::make($validatedData['new_password'])
            ]);
        }

        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully.');
    }
}
