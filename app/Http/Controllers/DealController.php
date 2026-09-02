<?php

namespace App\Http\Controllers;

use App\Enums\DealStatuses;
use App\Models\Deal;
use App\Models\Developer;
use App\Models\Compound;
use App\Models\UptownType;


use Illuminate\Http\Request;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Http\Controllers\Controller;

class DealController extends Controller
{

    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword   = $request->get('keyword');

        $dealsQuery = Deal::with(['developer', 'compound', 'uptownType'])
            ->when($request->developer_id, fn($q) => $q->where('developer_id', $request->developer_id))
            ->when($request->compound_id, fn($q) => $q->where('compound_id', $request->compound_id))
            ->when($request->uptown_type_id, fn($q) => $q->where('uptown_type_id', $request->uptown_type_id))
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('number_of_units', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhereHas('developer', fn($sub) => $sub->where('name_en', 'like', "%{$keyword}%")->orWhere('name_ar', 'like', "%{$keyword}%"))
                        ->orWhereHas('compound', fn($sub) => $sub->where('compound_name', 'like', "%{$keyword}%"))
                        ->orWhereHas('uptownType', fn($sub) => $sub->where('name_en', 'like', "%{$keyword}%")->orWhere('name_ar', 'like', "%{$keyword}%"));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy($sortField, $sortOrder);

        $deals = $dealsQuery->paginate(30);

        // Count per status
        $dealsStatusCounts = Deal::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = DealStatuses::cases(); // Get all enum cases

        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('id')->pluck('id', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();

        return view('deals.index', compact(
            'deals',
            'sortField',
            'sortOrder',
            'developers',
            'compounds',
            'uptownTypes',
            'statuses',
            'dealsStatusCounts'
        ));
    }



    public function create()
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();

        $statuses = DealStatuses::labels();
        return view('deals.create', compact('developers', 'compounds', 'uptownTypes', 'statuses'));
    }

    public function store(StoreDealRequest $request)
    {
        Deal::create($request->validated());
        return redirect()->route('deals.index')->with('success', 'Created successfully');
    }

    public function show(Deal $deal)
    {
        return view('deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();

        $statuses = DealStatuses::labels();
        return view('deals.edit', compact('deal', 'developers', 'compounds', 'uptownTypes', 'statuses'));
    }

    public function update(UpdateDealRequest $request, Deal $deal)
    {
        $deal->update($request->validated());
        return redirect()->route('deals.index')->with('success', 'Updated successfully.');
    }
}
