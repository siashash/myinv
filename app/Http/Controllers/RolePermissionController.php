<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('role_name')->get();
        $permissions = Permission::orderBy('module_name')->get();
        $rolePermissions = RolePermission::with(['role', 'permission'])->orderBy('role_id')->orderBy('permission_id')->get();

        return view('user_management.role_permissions.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRolePermission($request);

        RolePermission::create($validated);

        return redirect()->route('um.role_permissions.index')->with('success', 'Role permission created successfully.');
    }

    public function edit(int $roleId, int $permissionId)
    {
        $roles = Role::orderBy('role_name')->get();
        $permissions = Permission::orderBy('module_name')->get();

        $rolePermission = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->firstOrFail();

        return view('user_management.role_permissions.edit', compact('roles', 'permissions', 'rolePermission'));
    }

    public function update(Request $request, int $roleId, int $permissionId)
    {
        $validated = $this->validateRolePermission($request, [$roleId, $permissionId]);

        RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->update($validated);

        return redirect()->route('um.role_permissions.index')->with('success', 'Role permission updated successfully.');
    }

    public function destroy(int $roleId, int $permissionId)
    {
        RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();

        return redirect()->route('um.role_permissions.index')->with('success', 'Role permission deleted successfully.');
    }

    private function validateRolePermission(Request $request, ?array $ignoreComposite = null): array
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permission_id' => ['required', 'exists:permissions,id'],
            'can_view' => ['nullable', 'boolean'],
            'can_add' => ['nullable', 'boolean'],
            'can_edit' => ['nullable', 'boolean'],
            'can_delete' => ['nullable', 'boolean'],
        ]);

        $validated['role_id'] = (int) $validated['role_id'];
        $validated['permission_id'] = (int) $validated['permission_id'];
        $validated['can_view'] = $request->boolean('can_view');
        $validated['can_add'] = $request->boolean('can_add');
        $validated['can_edit'] = $request->boolean('can_edit');
        $validated['can_delete'] = $request->boolean('can_delete');

        if (! $validated['can_view'] && ! $validated['can_add'] && ! $validated['can_edit'] && ! $validated['can_delete']) {
            throw ValidationException::withMessages([
                'can_view' => 'Please select at least one action: view, add, edit, or delete.',
            ]);
        }

        $existing = RolePermission::where('role_id', $validated['role_id'])
            ->where('permission_id', $validated['permission_id']);

        if ($ignoreComposite !== null) {
            if ($validated['role_id'] === $ignoreComposite[0] && $validated['permission_id'] === $ignoreComposite[1]) {
                return $validated;
            }
        }

        if ($existing->exists()) {
            throw ValidationException::withMessages([
                'permission_id' => 'This role-permission mapping already exists.',
            ]);
        }

        return $validated;
    }
}
