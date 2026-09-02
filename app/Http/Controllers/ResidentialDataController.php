<?php

namespace App\Http\Controllers;

use App\Models\ResidentialData;
use Illuminate\Http\Request;

class ResidentialDataController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        
        $query = ResidentialData::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('field_name', 'LIKE', "%{$keyword}%")
                  ->orWhere('label_en', 'LIKE', "%{$keyword}%");
            });
        }

        $fields = $query->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('residential-data.index', compact('fields', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('residential-data.create');
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

        ResidentialData::create($validated);

        return redirect()->route('residential-data.index')->with('success', 'Field created successfully');
    }

    public function edit(ResidentialData $residentialDatum)
    {
        return view('residential-data.edit', ['field' => $residentialDatum]);
    }

    public function update(Request $request, ResidentialData $residentialDatum)
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

        $residentialDatum->update($validated);

        return redirect()->route('residential-data.index')->with('success', 'Field updated successfully');
    }

    public function destroy(ResidentialData $residentialDatum)
    {
        $residentialDatum->delete();
        return redirect()->route('residential-data.index')->with('success', 'Field deleted successfully');
    }
}
