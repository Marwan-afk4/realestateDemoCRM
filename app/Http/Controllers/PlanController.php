<?php

namespace App\Http\Controllers;

use App\Models\Plan;


use Illuminate\Http\Request;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        $plans = Plan::query();

        if ($keyword) {
            $plans->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->orWhere('price', 'LIKE', "%{$keyword}%");
            });
        }

        $plans = $plans->orderBy($sortField, $sortOrder)->paginate(30);


        return view('plans.index', compact('plans','sortField','sortOrder'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();

        // Calculate price_after_discount based on discount type and value
        $price_after_discount = $data['price'];

        if ($request->discount_type === 'fixed' && $request->discount_value) {
            $price_after_discount = $data['price'] - $request->discount_value;
        } elseif ($request->discount_type === 'percentage' && $request->discount_value) {
            $price_after_discount = $data['price'] - ($data['price'] * $request->discount_value / 100);
        }

        // Ensure price_after_discount is not negative
        $price_after_discount = max(0, $price_after_discount);

        $data['price_after_discount'] = $price_after_discount;

        Plan::create($data);
        return redirect()->route('plans.index')->with('success', 'Plan created successfully');
    }

    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $data = $request->validated();

        // Calculate the new price_after_discount if price or discount details are being updated
        if ($request->has('price') || $request->has('discount_type') || $request->has('discount_value')) {
            $price = $request->price ?? $plan->price;
            $discount_type = $request->discount_type ?? $plan->discount_type;
            $discount_value = $request->discount_value ?? $plan->discount_value;

            $price_after_discount = $price;

            if ($discount_type === 'fixed' && $discount_value) {
                $price_after_discount = $price - $discount_value;
            } elseif ($discount_type === 'percentage' && $discount_value) {
                $price_after_discount = $price - ($price * $discount_value / 100);
            }

            // Ensure price_after_discount is not negative
            $price_after_discount = max(0, $price_after_discount);

            $data['price_after_discount'] = $price_after_discount;
        }

        $plan->update($data);
        return redirect()->route('plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        try {
            $plan->delete();

            return redirect()
                ->route('plans.index')
                ->with('success', __('Plan deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('plans.index')
                ->with('error', __('Failed to delete plan. Please try again.'));
        }
    }
}
