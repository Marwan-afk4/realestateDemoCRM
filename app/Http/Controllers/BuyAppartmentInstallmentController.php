<?php

namespace App\Http\Controllers;

use App\Models\BuyAppartmentInstallment;
use App\Models\Uptown;
use App\Models\User;
use Illuminate\Http\Request;

class BuyAppartmentInstallmentController extends Controller
{
    /**
     * Display a listing of the installment requests.
     */
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'DESC');
        $keyword = $request->get('keyword');

        $installments = BuyAppartmentInstallment::with(['user', 'apartment'])
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('phone', 'LIKE', "%{$keyword}%");
                })
                ->orWhere('city', 'LIKE', "%{$keyword}%")
                ->orWhere('area', 'LIKE', "%{$keyword}%");
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('apartment-installments.index', compact('installments', 'sortField', 'sortOrder'));
    }

    public function create()
    {
        return view('apartment-installments.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'apartment_id' => 'nullable|exists:uptowns,id',
            'age' => 'required|integer|min:18',
            'identity_front_image' => 'required|image|max:5120',
            'identity_back_image' => 'required|image|max:5120',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'monthly_installment' => 'nullable|numeric|min:0',
            'years_of_installment' => 'required|integer|min:3|max:20',
            'deposit_percetage' => 'required|numeric|min:5|max:80',
        ]);

        $data['identity_front_image'] = $request->file('identity_front_image')->store('installments/identity', 'public');
        $data['identity_back_image'] = $request->file('identity_back_image')->store('installments/identity', 'public');
        $data['status'] = 'pending';

        $installment = BuyAppartmentInstallment::create($data);

        return redirect()->route('apartment-installments.show', $installment)
            ->with('success', __('Mortgage request created successfully'));
    }

    /**
     * Display the specified installment request.
     */
    public function show(BuyAppartmentInstallment $apartmentInstallment)
    {
        $apartmentInstallment->load(['user', 'apartment']);
        return view('apartment-installments.show', [
            'buyAppartmentInstallment' => $apartmentInstallment,
            'installment' => $apartmentInstallment
        ]);
    }

    /**
     * Update the status of the request.
     */
    public function updateStatus(Request $request, BuyAppartmentInstallment $apartmentInstallment)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,approved,rejected',
        ]);

        $apartmentInstallment->update(['status' => $request->status]);

        return back()->with('success', __('Status updated successfully'));
    }

    /**
     * Remove the specified request from storage.
     */
    public function destroy(BuyAppartmentInstallment $apartmentInstallment)
    {
        $apartmentInstallment->delete();
        return redirect()->route('apartment-installments.index')->with('success', __('Request deleted successfully'));
    }

    private function formData(): array
    {
        $years = [];
        for ($year = 3; $year <= 20; $year++) {
            $years[$year] = $year;
        }

        return [
            'users' => User::query()->orderBy('first_name')->get()
                ->mapWithKeys(fn (User $user) => [$user->id => trim($user->full_name).' ('.$user->phone.')'])
                ->all(),
            'apartments' => Uptown::query()->orderBy('id')->get()
                ->mapWithKeys(fn (Uptown $unit) => [$unit->id => $unit->name_en ?: ($unit->description ?: '#'.$unit->id)])
                ->all(),
            'years' => $years,
        ];
    }
}
