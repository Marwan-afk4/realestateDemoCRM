<?php

namespace App\Http\Controllers;

use App\Models\Uptown;
use App\Models\Developer;
use App\Models\Compound;
use App\Models\UptownType;
use App\Models\UnitsImage;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUptownRequest;
use App\Http\Requests\UpdateUptownRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UptownController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');

        if ($sortField === 'name') {
            $sortField = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        } elseif ($sortField === 'description') {
            $sortField = app()->getLocale() === 'ar' ? 'description_ar' : 'description_en';
        }

        $keyword = $request->get('keyword');

        $uptowns = Uptown::with(['developer', 'compound', 'uptownType'])
            ->when($request->get('compound_id'), function ($query, $compoundId) {
                $query->where('compound_id', $compoundId);
            })
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name_en', 'LIKE', "%{$keyword}%")
                      ->orWhere('name_ar', 'LIKE', "%{$keyword}%")
                      ->orWhere('description_en', 'LIKE', "%{$keyword}%")
                      ->orWhere('description_ar', 'LIKE', "%{$keyword}%")
                      ->orWhere('status', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('developer', function ($devQuery) use ($keyword) {
                          $devQuery->where('name_en', 'LIKE', "%{$keyword}%")
                                   ->orWhere('name_ar', 'LIKE', "%{$keyword}%");
                      })
                      ->orWhereHas('compound', function ($compQuery) use ($keyword) {
                          $compQuery->where('compound_name', 'LIKE', "%{$keyword}%");
                      });
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('uptowns.index', compact('uptowns', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        $developers = Developer::all();
        $compounds = Compound::with('developer')->get(); // Load all compounds with developer info
        $uptownTypes = UptownType::where('status', 'active')->get();

        return view('uptowns.create', compact('developers', 'compounds', 'uptownTypes'));
    }

    public function store(StoreUptownRequest $request)
    {
        $data = $request->validated();

        // Checkboxes: columns are enum('1','0') — must pass string, not integer
        $data['cash'] = (isset($data['cash']) && $data['cash']) ? '1' : '0';
        $data['installment'] = (isset($data['installment']) && $data['installment']) ? '1' : '0';

        // Handle file uploads
        if ($request->hasFile('floor_plan_image')) {
            $data['floor_plan_image'] = $request->file('floor_plan_image')->store('uptowns/floor_plans', 'public');
        }

        if ($request->hasFile('master_plan_image')) {
            $data['master_plan_image'] = $request->file('master_plan_image')->store('uptowns/master_plans', 'public');
        }

        // Remove unit_images from data before creating uptown
        unset($data['unit_images']);

        $uptown = Uptown::create($data);

        // Handle unit images upload
        if ($request->hasFile('unit_images')) {
            foreach ($request->file('unit_images') as $image) {
                $imagePath = $image->store('uptowns/unit_images', 'public');

                UnitsImage::create([
                    'uptown_id' => $uptown->id,
                    'image' => $imagePath,
                ]);
            }
        }

        return redirect()->route('uptowns.index')->with('success', 'Uptown created successfully');
    }

    public function show(Uptown $uptown)
    {
        $uptown->load(['developer', 'compound', 'uptownType', 'images', 'leads']);
        return view('uptowns.show', compact('uptown'));
    }

    public function edit(Uptown $uptown)
    {
        $developers = Developer::all();
        $compounds = Compound::with('developer')->get(); // Load all compounds
        $uptownTypes = UptownType::where('status', 'active')->get();

        return view('uptowns.edit', compact('uptown', 'developers', 'compounds', 'uptownTypes'));
    }

    public function update(UpdateUptownRequest $request, Uptown $uptown)
    {
        $data = $request->validated();

        // Checkboxes: columns are enum('1','0') — must pass string, not integer
        $data['cash'] = (isset($data['cash']) && $data['cash']) ? '1' : '0';
        $data['installment'] = (isset($data['installment']) && $data['installment']) ? '1' : '0';

        // Handle file uploads
        if ($request->hasFile('floor_plan_image')) {
            // Delete old file if exists
            if ($uptown->floor_plan_image) {
                Storage::disk('public')->delete($uptown->floor_plan_image);
            }
            $data['floor_plan_image'] = $request->file('floor_plan_image')->store('uptowns/floor_plans', 'public');
        }

        if ($request->hasFile('master_plan_image')) {
            // Delete old file if exists
            if ($uptown->master_plan_image) {
                Storage::disk('public')->delete($uptown->master_plan_image);
            }
            $data['master_plan_image'] = $request->file('master_plan_image')->store('uptowns/master_plans', 'public');
        }

        // Handle deleted images
        if ($request->filled('deleted_images')) {
            $deletedImageIds = explode(',', $request->deleted_images);
            $deletedImages = UnitsImage::whereIn('id', $deletedImageIds)->get();

            foreach ($deletedImages as $image) {
                // Delete file from storage
                if ($image->image) {
                    Storage::disk('public')->delete($image->image);
                }
                // Delete record from database
                $image->delete();
            }
        }

        // Remove unit_images and deleted_images from data before updating uptown
        unset($data['unit_images'], $data['deleted_images']);

        $uptown->update($data);

        // Handle new unit images upload
        if ($request->hasFile('unit_images')) {
            foreach ($request->file('unit_images') as $image) {
                $imagePath = $image->store('uptowns/unit_images', 'public');

                UnitsImage::create([
                    'uptown_id' => $uptown->id,
                    'image' => $imagePath,
                ]);
            }
        }

        return redirect()->route('uptowns.index')->with('success', 'Uptown updated successfully');
    }

    public function destroy(Uptown $uptown)
    {
        // Delete associated files
        if ($uptown->floor_plan_image) {
            Storage::disk('public')->delete($uptown->floor_plan_image);
        }
        if ($uptown->master_plan_image) {
            Storage::disk('public')->delete($uptown->master_plan_image);
        }

        // Delete all unit images
        foreach ($uptown->images as $image) {
            if ($image->image) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        $uptown->delete();

        return redirect()->route('uptowns.index')->with('success', 'Uptown deleted successfully');
    }

    public function getCompoundsByDeveloper(Request $request)
    {
        try {
            $developerId = $request->get('developer_id');

            if (!$developerId) {
                return response()->json([]);
            }

            $compounds = Compound::where('developer_id', $developerId)
                ->select('id', 'compound_name as name')
                ->get();

            return response()->json($compounds);
        } catch (\Exception $e) {
            Log::error('Error fetching compounds: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load compounds'], 500);
        }
    }
}
