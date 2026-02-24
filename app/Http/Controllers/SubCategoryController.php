<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('category_name')->get();
        $subCategories = SubCategory::with('category')->orderBy('id', 'desc')->get();

        return view('subcategories.index', compact('categories', 'subCategories'));
    }

    public function store(Request $request)
    {
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
        $categories = Category::orderBy('category_name')->get();

        return view('subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subcategory)
    {
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
        $subcategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'Sub-category deleted successfully.');
    }
}
