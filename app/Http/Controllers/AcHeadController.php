<?php

namespace App\Http\Controllers;

use App\Models\AcHead;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcHeadController extends Controller
{
    private const MODULE_NAMES = ['ac-head', 'accounts head', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $acHeads = AcHead::orderBy('id', 'desc')->get();
        return view('ac_heads.index', compact('acHeads', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'ac_headname' => ['required', 'string', 'max:255', 'unique:ac_head,ac_headname'],
            'mode' => ['required', Rule::in(['Credit', 'Debit'])],
        ]);

        AcHead::create($validated);

        return redirect()->route('ac_heads.index')->with('success', 'Accounts head created successfully.');
    }

    public function edit(AcHead $acHead)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        return view('ac_heads.edit', compact('acHead'));
    }

    public function update(Request $request, AcHead $acHead)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'ac_headname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ac_head', 'ac_headname')->ignore($acHead->id),
            ],
            'mode' => ['required', Rule::in(['Credit', 'Debit'])],
        ]);

        $acHead->update($validated);

        return redirect()->route('ac_heads.index')->with('success', 'Accounts head updated successfully.');
    }

    public function destroy(AcHead $acHead)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $acHead->delete();
        return redirect()->route('ac_heads.index')->with('success', 'Accounts head deleted successfully.');
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
