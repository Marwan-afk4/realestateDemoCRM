<?php

namespace App\Http\Controllers;

use App\Enums\ActivationStatus;
use App\Models\UptownType;


use Illuminate\Http\Request;
use App\Http\Requests\StoreUptownTypeRequest;
use App\Http\Requests\UpdateUptownTypeRequest;
use App\Http\Controllers\Controller;

class UptownTypeController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');

        if ($sortField === 'name') {
            $sortField = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        }

        $keyword = $request->get('keyword');
        $uptownTypes = UptownType::when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name_en', 'LIKE', "%{$keyword}%")
                      ->orWhere('name_ar', 'LIKE', "%{$keyword}%");
                })->orWhere('status', 'LIKE', "%{$keyword}%");
            })
        ->orderBy($sortField, $sortOrder)->paginate(30);

        return view('uptown-types.index', compact('uptownTypes','sortField','sortOrder'));
    }

    public function create()
    {
        $status = ActivationStatus::labels();
        return view('uptown-types.create', compact('status'));
    }

    public function store(StoreUptownTypeRequest $request)
    {
        UptownType::create($request->validated());
        return redirect()->route('uptown-types.index')->with('success', 'Created successfully');
    }

    public function show(UptownType $uptownType)
    {
        return view('uptown-types.show', compact('uptownType'));
    }

    public function edit(UptownType $uptownType)
    {
        $status = ActivationStatus::labels();
        return view('uptown-types.edit', compact('uptownType','status'));
    }

    public function update(UpdateUptownTypeRequest $request, UptownType $uptownType)
    {
        $uptownType->update($request->validated());
        return redirect()->route('uptown-types.index')->with('success', 'Updated successfully.');
    }

    public function getUptownTypes()
    {
        $uptownTypes = UptownType::where('status', 'active')->get();

        $data =[
            'uptownTypes' => $uptownTypes
        ];

        return response()->json($data);
    }
}
