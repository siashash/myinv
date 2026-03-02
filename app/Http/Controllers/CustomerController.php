<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private const MODULE_NAMES = ['customer', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $customers = Customer::orderBy('customer_id', 'desc')->get();
        return view('customers.index', compact('customers', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'boolean'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'boolean'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
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
