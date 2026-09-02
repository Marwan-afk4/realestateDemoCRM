<?php

namespace App\Http\Controllers;

use App\Enums\ActivationStatus;
use App\Models\User;
use App\Models\Plan;


use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword = $request->get('keyword');

        $users = User::where('role', 'user')
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
                });
            })
        ->orderBy($sortField, $sortOrder)->paginate(30);

        return view('users.index', compact('users','sortField','sortOrder'));
    }

    public function create()
    {
        $statuses = ActivationStatus::labels();
        return view('users.create', compact('statuses'));
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());
        return redirect()->route('users.index')->with('success', 'Created successfully');
    }

    public function show(User $user)
    {
        $user->load([
            'sellRequests.developer',
            'sellRequests.compound',
            'sellRequests.uptownType',
            'sellRequests.unitSubType',
            'sellRequests.images',
            'buyAppartmentInstallments.apartment'
        ]);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $statuses = ActivationStatus::labels();
        return view('users.edit', compact('user','statuses'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('users.index')->with('success', 'Updated successfully.');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', __('User deleted successfully.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Failed to delete user. Please try again.'));
        }
    }
}
