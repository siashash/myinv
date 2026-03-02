<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private const MODULE_NAMES = ['units', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $units = Unit::orderBy('id', 'desc')->get();
        return view('units.index', compact('units', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'base_unit' => ['required', 'string', 'max:50'],
            'sales_unit' => ['required', 'string', 'max:50'],
            'conversion_factor' => ['required', 'numeric', 'gt:0'],
        ]);

        $validated['base_unit'] = strtoupper(trim($validated['base_unit']));
        $validated['sales_unit'] = strtoupper(trim($validated['sales_unit']));

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'base_unit' => ['required', 'string', 'max:50'],
            'sales_unit' => ['required', 'string', 'max:50'],
            'conversion_factor' => ['required', 'numeric', 'gt:0'],
        ]);

        $validated['base_unit'] = strtoupper(trim($validated['base_unit']));
        $validated['sales_unit'] = strtoupper(trim($validated['sales_unit']));

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $isInUse = $unit->products()->exists();

        if ($isInUse) {
            return redirect()->route('units.index')
                ->with('error', 'Unit cannot be deleted because it is used in product master.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
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
