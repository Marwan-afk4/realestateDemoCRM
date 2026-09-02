<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatuses;
use App\Models\Lead;
use App\Models\Brocker;
use App\Models\Uptown;
use App\Models\MarketingAgency;


use Illuminate\Http\Request;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        $leads = Lead::with(['brocker', 'uptown', 'marketing_agency'])
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('brocker', function ($q) use ($keyword) {
                    $q->where('lead_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('lead_phone', 'LIKE', "%{$keyword}%")
                        ->orWhere('sales_man_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('sales_man_phone', 'LIKE', "%{$keyword}%");
                });
            })
            ->orderBy($sortField, $sortOrder)->paginate(30);

        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $brockers = Brocker::orderBy('id')->pluck('id', 'id')->toArray();
        $uptowns = Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $marketing_agencies = MarketingAgency::orderBy('name')->pluck('name', 'id')->toArray();

        return view('leads.index', compact('leads', 'sortField', 'sortOrder', 'brockers', 'uptowns', 'marketing_agencies'));
    }

    public function create()
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        // $brockers = Brocker::orderBy('id')->pluck('id', 'id')->toArray();
        $uptowns = Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $marketing_agencies = MarketingAgency::orderBy('name')->pluck('name', 'id')->toArray();

        $leadStatuses = LeadStatuses::labels();
        return view('leads.create', compact('uptowns', 'marketing_agencies', 'leadStatuses'));
    }

    public function store(StoreLeadRequest $request)
    {
        Lead::create($request->validated());
        return redirect()->route('leads.index')->with('success', 'Created successfully');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $brockers = Brocker::orderBy('id')->pluck('id', 'id')->toArray();
        $uptowns = Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $marketing_agencies = MarketingAgency::orderBy('name')->pluck('name', 'id')->toArray();
        $leadStatuses = LeadStatuses::labels();

        return view('leads.edit', compact('lead', 'brockers', 'uptowns', 'marketing_agencies', 'leadStatuses'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());
        return redirect()->route('leads.index')->with('success', 'Updated successfully.');
    }
}
