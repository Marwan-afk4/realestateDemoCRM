<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'DESC');
        $keyword = $request->get('keyword');
        $contracts = Contract::query();

        if ($keyword) {
            $contracts->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('pages', 'LIKE', "%{$keyword}%");
            });
        }

        $contracts = $contracts->orderBy($sortField, $sortOrder)->paginate(30);

        return view('contracts.index', compact('contracts', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('contracts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'pages' => 'required|array|min:1',
            'pages.*' => 'required|string',
        ]);

        Contract::create($data);
        return redirect()->route('contracts.index')->with('success', 'Contract created successfully');
    }

    public function show(Contract $contract)
    {
        $contract->load('agreements.user');
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'pages' => 'required|array|min:1',
            'pages.*' => 'required|string',
        ]);

        $contract->update($data);
        return redirect()->route('contracts.index')->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract)
    {
        try {
            $contract->delete();
            return redirect()->route('contracts.index')->with('success', 'Contract deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('contracts.index')->with('error', 'Failed to delete contract.');
        }
    }
}
