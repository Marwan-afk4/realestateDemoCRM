<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuyAppartmentInstallmentRequest;
use App\Models\BuyAppartmentInstallment;
use App\trait\image;
use Illuminate\Http\Request;

class BuyAppartmentInstallmentController extends Controller
{
    use image;

    /**
     * Store a new installment request.
     */
    public function store(StoreBuyAppartmentInstallmentRequest $request)
    {
        $data = $request->validated();

        // Handle base64 image uploads
        if ($request->filled('identity_front_image')) {
            $data['identity_front_image'] = $this->storeBase64Image($request->identity_front_image, 'installments/identity');
        }

        if ($request->filled('identity_back_image')) {
            $data['identity_back_image'] = $this->storeBase64Image($request->identity_back_image, 'installments/identity');
        }

        // Set the authenticated user ID
        $data['user_id'] = auth()->id();

        $installmentRequest = BuyAppartmentInstallment::create($data);

        return response()->json([
            'status' => 'success',
            'message' => __('Installment request submitted successfully'),
            'data' => $installmentRequest
        ], 201);
    }

    /**
     * Display a listing of the user's installment requests.
     */
    public function index()
    {
        $requests = BuyAppartmentInstallment::where('user_id', auth()->id())->with('apartment')->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }
}
