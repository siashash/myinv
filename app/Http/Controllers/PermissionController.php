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
        $availableModules = $this->availableModules();

        return view('user_management.permissions.index', compact('permissions', 'availableModules'));
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
        $availableModules = $this->availableModules();

        return view('user_management.permissions.edit', compact('permission', 'availableModules'));
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

    private function availableModules(): array
    {
        return [
            'Master' => [
                'category',
                'sub-category',
                'units',
                'product',
                'supplier',
                'customer',
                'ac-head',
            ],
            'Transaction' => [
                'purchase',
                'sales',
            ],
            'Return' => [
                'purchase-return',
                'sales-return',
            ],
            'Payments' => [
                'purchase-payment',
            ],
            'Receipts' => [
                'sales-receipt',
            ],
            'Reports' => [
                'stock-report',
                'sales-report',
                'account-book',
                'cash-book',
                'bank-book',
                'sundry-creditors',
            ],
            'User Management' => [
                'users',
                'roles',
                'permissions',
                'role-permissions',
            ],
        ];
    }
}
