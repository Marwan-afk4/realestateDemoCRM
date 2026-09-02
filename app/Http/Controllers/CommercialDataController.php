<?php

namespace App\Http\Controllers;

use App\Models\CommercialData;
use Illuminate\Http\Request;

class CommercialDataController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        
        $fields = CommercialData::when($keyword, function ($query, $keyword) {
                $query->where('field_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('label_en', 'LIKE', "%{$keyword}%");
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('commercial-data.index', compact('fields', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('commercial-data.create');
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

        CommercialData::create($validated);

        return redirect()->route('commercial-data.index')->with('success', 'Field created successfully');
    }

    public function edit(CommercialData $commercialDatum)
    {
        return view('commercial-data.edit', ['field' => $commercialDatum]);
    }

    public function update(Request $request, CommercialData $commercialDatum)
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

        $commercialDatum->update($validated);

        return redirect()->route('commercial-data.index')->with('success', 'Field updated successfully');
    }

    public function destroy(CommercialData $commercialDatum)
    {
        $commercialDatum->delete();
        return redirect()->route('commercial-data.index')->with('success', 'Field deleted successfully');
    }
}
