<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->get();
        return view('user_management.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_name' => ['required', 'string', 'max:255', 'unique:permissions,module_name'],
        ]);

        Permission::create($validated);

        return redirect()->route('um.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('user_management.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'module_name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'module_name')->ignore($permission->id)],
        ]);

        $permission->update($validated);

        return redirect()->route('um.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('um.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
