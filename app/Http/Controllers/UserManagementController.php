<?php

namespace App\Http\Controllers;

use App\Models\ManagedUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('role_name')->get();
        $users = ManagedUser::with('role')->orderBy('id', 'desc')->get();

        return view('user_management.users.index', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        ManagedUser::create([
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('um.users.index')->with('success', 'User created successfully.');
    }

    public function edit(ManagedUser $managedUser)
    {
        $roles = Role::orderBy('role_name')->get();
        return view('user_management.users.edit', compact('managedUser', 'roles'));
    }

    public function update(Request $request, ManagedUser $managedUser)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'role_id' => $validated['role_id'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $managedUser->update($payload);

        return redirect()->route('um.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(ManagedUser $managedUser)
    {
        $managedUser->delete();
        return redirect()->route('um.users.index')->with('success', 'User deleted successfully.');
    }
}
