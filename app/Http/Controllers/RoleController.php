<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id', 'desc')->get();
        return view('user_management.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255', 'unique:roles,role_name'],
        ]);

        Role::create($validated);

        return redirect()->route('um.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('user_management.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255', Rule::unique('roles', 'role_name')->ignore($role->id)],
        ]);

        $role->update($validated);

        return redirect()->route('um.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('um.roles.index')->with('success', 'Role deleted successfully.');
    }
}
