<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeData;
use Illuminate\Http\Request;

class AdministrativeDataController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        
        $fields = AdministrativeData::when($keyword, function ($query, $keyword) {
                $query->where('field_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('label_en', 'LIKE', "%{$keyword}%");
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('administrative-data.index', compact('fields', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('administrative-data.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'label_ar' => 'nullable|string|max:255',
            'type' => 'required|in:text,number,boolean,select',
            'is_required' => 'required|boolean',
            'options' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validated['type'] === 'select' && !empty($validated['options'])) {
            $validated['options'] = array_map('trim', explode("\n", str_replace("\r", "", $validated['options'])));
        } else {
            $validated['options'] = null;
        }

        AdministrativeData::create($validated);

        return redirect()->route('administrative-data.index')->with('success', 'Field created successfully');
    }

    public function edit(AdministrativeData $administrativeDatum)
    {
        return view('administrative-data.edit', ['field' => $administrativeDatum]);
    }

    public function update(Request $request, AdministrativeData $administrativeDatum)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'label_ar' => 'nullable|string|max:255',
            'type' => 'required|in:text,number,boolean,select',
            'is_required' => 'required|boolean',
            'options' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validated['type'] === 'select' && !empty($validated['options'])) {
            $validated['options'] = array_map('trim', explode("\n", str_replace("\r", "", $validated['options'])));
        } else {
            $validated['options'] = null;
        }

        $administrativeDatum->update($validated);

        return redirect()->route('administrative-data.index')->with('success', 'Field updated successfully');
    }

    public function destroy(AdministrativeData $administrativeDatum)
    {
        $administrativeDatum->delete();
        return redirect()->route('administrative-data.index')->with('success', 'Field deleted successfully');
    }
}
