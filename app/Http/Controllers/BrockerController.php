<?php

namespace App\Http\Controllers;

use App\Models\Brocker;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBrockerRequest;
use App\Http\Requests\UpdateBrockerRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BrockerController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');
        $brockers = Brocker::with(['user','plan'])
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%")
                      ->orWhere('phone', 'LIKE', "%{$keyword}%");
                })->orWhereHas('plan', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            })
        ->orderBy($sortField, $sortOrder)->paginate(30);

        $users = User::orderBy('id')->pluck('id', 'id')->toArray();
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();

        return view('brockers.index', compact('brockers','sortField','sortOrder','users','plans'));
    }

    public function create()
    {
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();
        return view('brockers.create', compact('plans'));
    }

    public function store(StoreBrockerRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Create the user first
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'brocker',
                'plan_id' => $request->plan_id,
            ]);

            $brokerRole = Role::findByName('broker', 'web');
            $user->assignRole($brokerRole);

            // Create the broker
            Brocker::create([
                'user_id' => $user->id,
                'plan_id' => $request->plan_id,
                'profit' => $request->profit,
                'number_of_deals' => $request->number_of_deals,
                'deals_done' => $request->deals_done,
                'comission_percentage' => $request->comission_percentage,
            ]);
        });

        return redirect()->route('brockers.index')->with('success', 'Broker created successfully');
    }

    public function show(Brocker $brocker)
    {
        return view('brockers.show', compact('brocker'));
    }

    public function edit(Brocker $brocker)
    {
        $brocker->load('user'); // Ensure user is loaded
        $plans = Plan::orderBy('name')->pluck('name', 'id')->toArray();
        return view('brockers.edit', compact('brocker','plans'));
    }

    public function update(UpdateBrockerRequest $request, Brocker $brocker)
    {
        DB::transaction(function () use ($request, $brocker) {
            // Update the user information
            $brocker->user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'plan_id' => $request->plan_id,
            ]);

            // Update the broker information
            $brocker->update([
                'plan_id' => $request->plan_id,
                'profit' => $request->profit,
                'number_of_deals' => $request->number_of_deals,
                'deals_done' => $request->deals_done,
                'comission_percentage' => $request->comission_percentage,
            ]);
        });

        return redirect()->route('brockers.index')->with('success', 'Broker updated successfully.');
    }

    public function destroy(Brocker $brocker)
    {
        try {
            $brocker->delete();

            return redirect()
                ->route('brockers.index')
                ->with('success', __('Broker deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('brockers.index')
                ->with('error', __('Failed to delete broker. Please try again.'));
        }
    }
}
