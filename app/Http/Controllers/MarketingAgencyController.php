<?php

namespace App\Http\Controllers;

use App\Models\MarketingAgency;
use Illuminate\Http\Request;

class MarketingAgencyController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        $agencies = MarketingAgency::withCount(['leads', 'agents'])->orderBy('name')->paginate(20);

        return view('marketing-agencies.index', compact('agencies'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        return view('marketing-agencies.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $agency = MarketingAgency::create($data);

        return redirect()->route('marketing-agencies.show', $agency)->with('success', __('Agency created.'));
    }

    public function show(MarketingAgency $marketingAgency)
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        $marketingAgency->load(['agents', 'leads.ticket']);

        return view('marketing-agencies.show', ['agency' => $marketingAgency]);
    }

    public function edit(MarketingAgency $marketingAgency)
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        return view('marketing-agencies.edit', ['agency' => $marketingAgency]);
    }

    public function update(Request $request, MarketingAgency $marketingAgency)
    {
        abort_unless(auth()->user()?->can('view-marketing-agencies'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $marketingAgency->update($data);

        return redirect()->route('marketing-agencies.show', $marketingAgency)->with('success', __('Agency updated.'));
    }
}
