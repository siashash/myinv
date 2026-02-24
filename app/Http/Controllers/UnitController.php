<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::orderBy('id', 'desc')->get();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
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
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
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
        $isInUse = $unit->products()->exists();

        if ($isInUse) {
            return redirect()->route('units.index')
                ->with('error', 'Unit cannot be deleted because it is used in product master.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
