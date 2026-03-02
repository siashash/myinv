<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    private const MODULE_NAMES = ['supplier', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $suppliers = Supplier::orderBy('supplier_id', 'desc')->get();
        return view('suppliers.index', compact('suppliers', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255', 'unique:suppliers,supplier_name'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'boolean'],
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'supplier_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('suppliers', 'supplier_name')->ignore($supplier->supplier_id, 'supplier_id'),
            ],
            'contact_name' => ['required', 'string', 'max:255'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'boolean'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    private function can(RolePermissionAccess $access, string $action): bool
    {
        foreach (self::MODULE_NAMES as $moduleName) {
            if ($access->allows($moduleName, $action)) {
                return true;
            }
        }

        return false;
    }
}
