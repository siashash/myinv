<?php

namespace App\Http\Controllers;

use App\Models\AcHead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcHeadController extends Controller
{
    public function index()
    {
        $acHeads = AcHead::orderBy('id', 'desc')->get();
        return view('ac_heads.index', compact('acHeads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ac_headname' => ['required', 'string', 'max:255', 'unique:ac_head,ac_headname'],
            'mode' => ['required', Rule::in(['Credit', 'Debit'])],
        ]);

        AcHead::create($validated);

        return redirect()->route('ac_heads.index')->with('success', 'Accounts head created successfully.');
    }

    public function edit(AcHead $acHead)
    {
        return view('ac_heads.edit', compact('acHead'));
    }

    public function update(Request $request, AcHead $acHead)
    {
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
        $acHead->delete();
        return redirect()->route('ac_heads.index')->with('success', 'Accounts head deleted successfully.');
    }
}
