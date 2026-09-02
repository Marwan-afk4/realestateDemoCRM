<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Models\Plan;


use Illuminate\Http\Request;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\PaymentMethod;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $tab = $request->get('tab', 'all');

        $payments = Payment::with(['paymentmethod','plan','brocker.user'])
			->when(request('payment_method_id'), function ($query) {
				$query->where('payment_method_id', request('payment_method_id'));
			})
			->when(request('plan_id'), function ($query) {
				$query->where('plan_id', request('plan_id'));
			})
			->when(request('keyword'), function ($query) {
				$keyword = request('keyword');
				$query->where(function($q) use ($keyword) {
					$q->whereHas('paymentmethod', function($pm) use ($keyword) {
						$pm->where('method_name', 'like', "%{$keyword}%");
					})
					->orWhereHas('plan', function($p) use ($keyword) {
						$p->where('name', 'like', "%{$keyword}%");
					})
					->orWhereHas('brocker.user', function($u) use ($keyword) {
						$u->where('first_name', 'like', "%{$keyword}%")
						  ->orWhere('last_name', 'like', "%{$keyword}%");
					});
				});
			})
			->when($tab !== 'all', function ($query) use ($tab) {
				$query->where('status', $tab);
			})
			->orderBy($sortField, $sortOrder)->paginate(30);

        $paymentmethods = PaymentMethod::orderBy('method_name')->pluck('method_name', 'id')->toArray();
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();

        return view('payments.index', compact('payments','sortField','sortOrder','paymentmethods','plans','tab'));
    }

    public function create()
    {
        $paymentMethods = Paymentmethod::orderBy('method_name')->pluck('method_name', 'id')->toArray();

        $brockers = Brocker::with('user')
        ->get()
        ->mapWithKeys(fn($brocker) => [$brocker->id => $brocker->user->full_name])
        ->toArray();


        $statuses = PaymentStatus::labels();
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();
        return view('payments.create', compact('paymentMethods','brockers','plans','statuses'));
    }

    public function store(StorePaymentRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('payments/receipt', 'public');
            $validatedData['receipt'] = $path;
        }

        Payment::create($validatedData);
        return redirect()->route('payments.index')->with('success', 'Created successfully');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $paymentMethods = Paymentmethod::orderBy('id')->pluck('id', 'id')->toArray();
        $users = User::orderBy('id')->pluck('id', 'id')->toArray();
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();
        return view('payments.edit', compact('payment','paymentMethods','users','plans'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        return redirect()->route('payments.index')->with('success', 'Updated successfully.');
    }

    public function approve(Payment $payment)
    {
        $payment->update([
            'status' => 'approved',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Payment approved successfully.');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Payment rejected successfully.');
    }


}
