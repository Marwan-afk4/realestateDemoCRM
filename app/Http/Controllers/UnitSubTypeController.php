<?php

namespace App\Http\Controllers;

use App\Models\UnitSubType;
use App\Models\UptownType;
use Illuminate\Http\Request;

class UnitSubTypeController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        
        $subTypes = UnitSubType::with('uptownType')
            ->when($keyword, function ($query, $keyword) {
                $query->where('name_en', 'LIKE', "%{$keyword}%")
                        ->orWhere('name_ar', 'LIKE', "%{$keyword}%");
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('unit-sub-types.index', compact('subTypes', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        $uptownTypes = UptownType::where('status', 'active')->get();
        return view('unit-sub-types.create', compact('uptownTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        UnitSubType::create($validated);

        return redirect()->route('unit-sub-types.index')->with('success', 'Sub-type created successfully');
    }

    public function edit(UnitSubType $unitSubType)
    {
        $uptownTypes = UptownType::where('status', 'active')->get();
        return view('unit-sub-types.edit', compact('unitSubType', 'uptownTypes'));
    }

    public function update(Request $request, UnitSubType $unitSubType)
    {
        $validated = $request->validate([
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $unitSubType->update($validated);

        return redirect()->route('unit-sub-types.index')->with('success', 'Sub-type updated successfully');
    }

    public function destroy(UnitSubType $unitSubType)
    {
        $unitSubType->delete();
        return redirect()->route('unit-sub-types.index')->with('success', 'Sub-type deleted successfully');
    }
}
