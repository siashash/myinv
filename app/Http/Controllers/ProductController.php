<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Support\RolePermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private const MODULE_NAMES = ['product', 'masters'];

    public function index()
    {
        $access = app(RolePermissionAccess::class);
        $canView = $this->can($access, 'view');
        $canAdd = $this->can($access, 'add');
        $canEdit = $this->can($access, 'edit');
        $canDelete = $this->can($access, 'delete');

        abort_unless($canView || $canAdd || $canEdit || $canDelete, 403);

        $categories = Category::orderBy('category_name')->get();
        $units = Unit::orderBy('base_unit')->orderBy('sales_unit')->get();
        $products = Product::with(['category', 'subCategory', 'unit'])
            ->orderBy('id', 'desc')
            ->get();

        return view('products.index', compact('categories', 'units', 'products', 'canAdd', 'canEdit', 'canDelete'));
    }

    public function store(Request $request)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'add'), 403);

        $validated = $this->validateProduct($request);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $categories = Category::orderBy('category_name')->get();
        $subCategories = SubCategory::where('category_id', $product->category_id)
            ->orderBy('sub_category_name')
            ->get();
        $units = Unit::orderBy('base_unit')->orderBy('sales_unit')->get();

        return view('products.edit', compact('product', 'categories', 'subCategories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'edit'), 403);

        $validated = $this->validateProduct($request, $product->id);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        abort_unless($this->can(app(RolePermissionAccess::class), 'delete'), 403);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function subCategories(Category $category)
    {
        $access = app(RolePermissionAccess::class);
        abort_unless(
            $this->can($access, 'view') || $this->can($access, 'add') || $this->can($access, 'edit'),
            403
        );

        $subCategories = SubCategory::where('category_id', $category->id)
            ->orderBy('sub_category_name')
            ->get(['id', 'sub_category_name']);

        return response()->json($subCategories);
    }

    private function validateProduct(Request $request, ?int $ignoreProductId = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['required', 'exists:sub_categories,id'],
            'product_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'product_name')->ignore($ignoreProductId),
            ],
            'product_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'product_code')->ignore($ignoreProductId),
            ],
            'hsn_code' => ['required', 'string', 'max:50'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'unit_id' => ['required', 'exists:units,id'],
            'discount_amount' => ['required', 'numeric', 'min:0', 'lte:purchase_price'],
            'opening_stock' => ['required', 'numeric', 'min:0'],
            'cgst_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'sgst_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'igst_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'boolean'],
        ]);

        $subCategoryBelongsToCategory = SubCategory::where('id', $validated['sub_category_id'])
            ->where('category_id', $validated['category_id'])
            ->exists();

        if (! $subCategoryBelongsToCategory) {
            throw ValidationException::withMessages([
                'sub_category_id' => 'Selected sub-category does not belong to selected category.',
            ]);
        }

        $unit = Unit::findOrFail((int) $validated['unit_id']);
        $validated['uom'] = $unit->base_unit;
        $validated['sales_uom'] = $unit->sales_unit;
        $conversionFactor = (float) ($unit->conversion_factor ?? 1);
        if ($conversionFactor <= 0) {
            $conversionFactor = 1;
        }
        $validated['conversion_factor'] = $conversionFactor;
        $validated['base_unit_id'] = null;
        $validated['sale_unit_id'] = null;
        $validated['sales_price_bu'] = round((float) $validated['purchase_price'], 2);
        $validated['sales_price_su'] = round((float) $validated['purchase_price'] / $conversionFactor, 2);

        $salePrice = round(
            (float) $validated['purchase_price'] - (float) $validated['discount_amount'],
            2
        );
        $salePrice = max(0, $salePrice);

        $totalTaxPercent = (float) $validated['cgst_percent']
            + (float) $validated['sgst_percent']
            + (float) $validated['igst_percent'];
        $validated['gst_percent'] = $totalTaxPercent;

        $validated['sale_price'] = $salePrice;
        $validated['final_price'] = round(
            $salePrice + ($salePrice * ($totalTaxPercent / 100)),
            2
        );

        return $validated;
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
