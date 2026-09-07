<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function create()
    {
        return view('contract-agreements.create', [
            'users' => User::query()->orderBy('first_name')->get()
                ->mapWithKeys(fn (User $user) => [$user->id => trim($user->full_name).' ('.$user->phone.')'])
                ->all(),
            'contracts' => Contract::query()->orderBy('title')->pluck('title', 'id')->all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('contract_agreements')->where(
                    fn ($query) => $query->where('contract_id', $request->input('contract_id'))
                ),
            ],
        ], [
            'user_id.unique' => __('This user already agreed to that contract.'),
        ]);

        $agreement = ContractAgreement::create($data);

        return redirect()->route('contract-agreements.show', $agreement)
            ->with('success', __('Contract agreement recorded.'));
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
