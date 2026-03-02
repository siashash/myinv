<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    private const MODULE_NAMES = ['sub-category', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $categories = Category::orderBy('category_name')->get();
        $subCategories = SubCategory::with('category')->orderBy('id', 'desc')->get();

        return view('subcategories.index', compact('categories', 'subCategories', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_name' => ['required', 'string', 'max:255', 'unique:sub_categories,sub_category_name'],
            'status' => ['required', 'boolean'],
        ]);

        SubCategory::create($validated);

        return redirect()->route('subcategories.index')->with('success', 'Sub-category created successfully.');
    }

    public function edit(SubCategory $subcategory)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $categories = Category::orderBy('category_name')->get();

        return view('subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_categories', 'sub_category_name')->ignore($subcategory->id),
            ],
            'status' => ['required', 'boolean'],
        ]);

        $subcategory->update($validated);

        return redirect()->route('subcategories.index')->with('success', 'Sub-category updated successfully.');
    }

    public function destroy(SubCategory $subcategory)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $subcategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'Sub-category deleted successfully.');
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
