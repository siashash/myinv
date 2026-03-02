<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    private const MODULE_NAMES = ['Category', 'masters'];

    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        $access = app(RolePermissionAccess::class);

        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        return view('categories.index', compact('categories', 'canView', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:categories,category_name'],
            'status' => ['required', 'boolean'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'category_name')->ignore($category->id),
            ],
            'status' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
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
