<?php

namespace App\Http\Controllers;

use App\Models\ContractAgreement;
use Illuminate\Http\Request;

class ContractAgreementController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'DESC');
        $keyword = $request->get('keyword');
        
        $agreements = ContractAgreement::with(['user', 'contract']);

        if ($keyword) {
            $agreements->whereHas('user', function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%");
            })->orWhereHas('contract', function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%");
            });
        }

        $agreements = $agreements->orderBy($sortField, $sortOrder)->paginate(30);

        return view('contract-agreements.index', compact('agreements', 'sortField', 'sortOrder'));
    }

    public function show($id)
    {
        $agreement = ContractAgreement::with(['user', 'contract'])->findOrFail($id);
        return view('contract-agreements.show', compact('agreement'));
    }

    public function destroy($id)
    {
        try {
            $agreement = ContractAgreement::findOrFail($id);
            $agreement->delete();
            return redirect()->route('contract-agreements.index')->with('success', 'Contract agreement deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('contract-agreements.index')->with('error', 'Failed to delete contract agreement.');
        }
    }
}
