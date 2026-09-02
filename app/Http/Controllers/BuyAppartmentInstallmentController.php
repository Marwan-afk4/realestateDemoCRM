<?php

namespace App\Http\Controllers;

use App\Models\BuyAppartmentInstallment;
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
}
