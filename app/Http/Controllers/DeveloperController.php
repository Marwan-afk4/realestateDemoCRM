<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\Place;
use App\Models\SalesDeveloper;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDeveloperRequest;
use App\Http\Requests\UpdateDeveloperRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeveloperController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'ASC');

        if ($sortField === 'name') {
            $sortField = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        } elseif ($sortField === 'description') {
            $sortField = app()->getLocale() === 'ar' ? 'description_ar' : 'description_en';
        }

        $developers = Developer::with(['places', 'sales_developer'])
            ->when(request('keyword'), function ($query) {
                $keyword = request('keyword');
                $query->where(function($q) use ($keyword) {
                    $q->where('name_en', 'like', "%{$keyword}%")
                      ->orWhere('name_ar', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%")
                      ->orWhere('description_en', 'like', "%{$keyword}%")
                      ->orWhere('description_ar', 'like', "%{$keyword}%");
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(12);

        return view('developers.index', compact('developers','sortField','sortOrder'));
    }

    public function create()
    {
        return view('developers.create');
    }

    public function store(StoreDeveloperRequest $request)
    {
        $validatedData = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('developers/images', 'public');
            $validatedData['image'] = $path;
        }

        // Calculate units and profit if not provided
        if (!isset($validatedData['units'])) {
            $validatedData['units'] = 0;
        }

        if (!isset($validatedData['total_profit'])) {
            $validatedData['total_profit'] = 0;
        }

        if (!isset($validatedData['deals_done'])) {
            $validatedData['deals_done'] = 0;
        }

        $developer = Developer::create($validatedData);

        // Handle places
        if ($request->has('places')) {
            foreach ($request->places as $placeName) {
                if (!empty(trim($placeName))) {
                    Place::create([
                        'developer_id' => $developer->id,
                        'place' => trim($placeName)
                    ]);
                }
            }
        }

        // Handle salesmen
        if ($request->has('salesmen')) {
            foreach ($request->salesmen as $salesman) {
                if (!empty(trim($salesman['name'])) || !empty(trim($salesman['phone']))) {
                    SalesDeveloper::create([
                        'developer_id' => $developer->id,
                        'sale_name' => trim($salesman['name']),
                        'sale_phone' => trim($salesman['phone'])
                    ]);
                }
            }
        }

        return redirect()
            ->route('developers.index')
            ->with('success', __('Developer created successfully.'));
    }

    public function show(Developer $developer)
    {
        return view('developers.show', compact('developer'));
    }

    public function edit(Developer $developer)
    {
        return view('developers.edit', compact('developer'));
    }

    public function update(UpdateDeveloperRequest $request, Developer $developer)
    {
        $validatedData = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($developer->image && Storage::disk('public')->exists($developer->image)) {
                Storage::disk('public')->delete($developer->image);
            }

            $path = $request->file('image')->store('developers/images', 'public');
            $validatedData['image'] = $path;
        }

        // Update developer
        $developer->update($validatedData);

        // Update places
        $developer->places()->delete(); // Remove existing places
        if ($request->has('places')) {
            foreach ($request->places as $placeName) {
                if (!empty(trim($placeName))) {
                    Place::create([
                        'developer_id' => $developer->id,
                        'place' => trim($placeName)
                    ]);
                }
            }
        }

        // Update salesmen
        $developer->sales_developer()->delete(); // Remove existing salesmen
        if ($request->has('salesmen')) {
            foreach ($request->salesmen as $salesman) {
                if (!empty(trim($salesman['name'])) || !empty(trim($salesman['phone']))) {
                    SalesDeveloper::create([
                        'developer_id' => $developer->id,
                        'sale_name' => trim($salesman['name']),
                        'sale_phone' => trim($salesman['phone'])
                    ]);
                }
            }
        }

        return redirect()
            ->route('developers.index')
            ->with('success', __('Developer updated successfully.'));
    }

    public function destroy(Developer $developer)
    {
        try {
            // Delete associated image if exists
            if ($developer->image && Storage::disk('public')->exists($developer->image)) {
                Storage::disk('public')->delete($developer->image);
            }

            $developer->delete();

            return redirect()
                ->route('developers.index')
                ->with('success', __('Developer deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('developers.index')
                ->with('error', __('Failed to delete developer. Please try again.'));
        }
    }
}
