<?php

namespace App\Http\Controllers;

use App\Enums\ActivationStatus;
use App\Models\PaymentMethod;


use Illuminate\Http\Request;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $paymentMethods = PaymentMethod::orderBy($sortField, $sortOrder)->paginate(30);


        return view('payment-methods.index', compact('paymentMethods','sortField','sortOrder'));
    }

    public function create()
    {
        $statuses = ActivationStatus::labels();
        return view('payment-methods.create',compact('statuses'));
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment-method/image', 'public');
            $validatedData['image'] = $path;
        }

        PaymentMethod::create($validatedData);
        return redirect()->route('payment-methods.index')->with('success', 'Created successfully');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $statuses = ActivationStatus::labels();
        return view('payment-methods.edit', compact('paymentMethod','statuses'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment-method/images', 'public');
            $validatedData['image'] = $path;
        }

        $paymentMethod->update($validatedData);
        return redirect()->route('payment-methods.index')->with('success', 'Updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();

            return redirect()
                ->route('payment-methods.index')
                ->with('success', __('Payment method deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('payment-methods.index')
                ->with('error', __('Failed to delete payment method. Please try again.'));
        }
    }
}
