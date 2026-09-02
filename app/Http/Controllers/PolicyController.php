<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $policies = Policy::orderBy($sortField, $sortOrder)->paginate(30);

        return view('policies.index', compact('policies', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('policies.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Policy::create($validatedData);

        return redirect()->route('policies.index')->with('success', __('Policy created successfully.'));
    }

    public function show(Policy $policy)
    {
        return view('policies.show', compact('policy'));
    }

    public function edit(Policy $policy)
    {
        return view('policies.edit', compact('policy'));
    }

    public function update(Request $request, Policy $policy)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $policy->update($validatedData);

        return redirect()->route('policies.index')->with('success', __('Policy updated successfully.'));
    }

    public function destroy(Policy $policy)
    {
        try {
            $policy->delete();

            return redirect()
                ->route('policies.index')
                ->with('success', __('Policy deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('policies.index')
                ->with('error', __('Failed to delete policy. Please try again.'));
        }
    }
}
