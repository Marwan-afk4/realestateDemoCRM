<?php

namespace App\Http\Controllers;

use App\Models\Ad;


use Illuminate\Http\Request;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Http\Controllers\Controller;
use App\trait\ImageUpload;

class AdController extends Controller
{
    use ImageUpload;


    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');

        if ($sortField === 'title') {
            $sortField = app()->getLocale() === 'ar' ? 'title_ar' : 'title_en';
        }

        $ads = Ad::orderBy($sortField, $sortOrder)->paginate(30);


        return view('ads.index', compact('ads','sortField','sortOrder'));
    }

    public function create()
    {
        return view('ads.create');
    }

    public function store(StoreAdRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ads/image', 'public');
            $validatedData['image'] = $path;
        }

        Ad::create($validatedData);
        return redirect()->route('ads.index')->with('success', 'Created successfully');
    }

    public function show(Ad $ad)
    {
        return view('ads.show', compact('ad'));
    }

    public function edit(Ad $ad)
    {
        return view('ads.edit', compact('ad'));
    }

    public function update(UpdateAdRequest $request, Ad $ad)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ads/images', 'public');
            $validatedData['image'] = $path;
        }

        $ad->update($validatedData);
        return redirect()->route('ads.index')->with('success', 'Updated successfully.');
    }


    public function destroy(Ad $ad)
    {
        try {
            $ad->delete();

            return redirect()
                ->route('ads.index')
                ->with('success', __('Ad deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('ads.index')
                ->with('error', __('Failed to delete ad. Please try again.'));
        }
    }
}
