<?php

namespace App\Http\Controllers;

use App\Models\Compound;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCompoundRequest;
use App\Http\Requests\UpdateCompoundRequest;
use App\Http\Controllers\Controller;

class CompoundController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $developerId = $request->get('developer_id');

        $query = Compound::with('developer')->withCount('uptwons');

        if ($developerId) {
            $query->where('developer_id', $developerId);
        }

        $compounds = $query->orderBy($sortField, $sortOrder)->paginate(30);

        // Get developer info if filtering by developer
        $developer = $developerId ? Developer::find($developerId) : null;

        return view('compounds.index', compact('compounds', 'sortField', 'sortOrder', 'developer', 'developerId'));
    }

    public function create(Request $request)
    {
        $developerId = $request->get('developer_id');
        $developer = $developerId ? Developer::find($developerId) : null;
        $developers = $this->developersForSelect();

        return view('compounds.create', compact('developer', 'developerId', 'developers'));
    }

    public function store(StoreCompoundRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('compounds', 'public');
        }

        $compound = Compound::create($data);

        // Redirect back to developer's compounds if created from developer context
        if ($compound->developer_id) {
            return redirect()->route('compounds.index', ['developer_id' => $compound->developer_id])
                ->with('success', 'Compound created successfully');
        }

        return redirect()->route('compounds.index')->with('success', 'Compound created successfully');
    }

    public function show(Compound $compound)
    {
        return view('compounds.show', compact('compound'));
    }

    public function edit(Compound $compound)
    {
        $developers = $this->developersForSelect();
        return view('compounds.edit', compact('compound', 'developers'));
    }

    public function update(UpdateCompoundRequest $request, Compound $compound)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($compound->image) {
                Storage::disk('public')->delete($compound->image);
            }
            $data['image'] = $request->file('image')->store('compounds', 'public');
        }

        $compound->update($data);

        // Redirect back to developer's compounds if updated from developer context
        if ($compound->developer_id) {
            return redirect()->route('compounds.index', ['developer_id' => $compound->developer_id])
                ->with('success', 'Compound updated successfully');
        }

        return redirect()->route('compounds.index')->with('success', 'Compound updated successfully');
    }

    private function developersForSelect()
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';

        return Developer::orderBy($nameColumn)->get();
    }
}
