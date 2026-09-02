<?php

namespace App\Http\Controllers;

use App\Enums\ActivationStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $admins = User::where('role', 'admin')
            ->with('roles')
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('email', 'LIKE', "%{$keyword}%")
                        ->orWhere('phone', 'LIKE', "%{$keyword}%");
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('admin-users.index', compact('admins'));
    }

    public function create()
    {
        $statuses = ActivationStatus::labels();
        $roles = Role::orderBy('name')->get();
        return view('admin-users.create', compact('statuses', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:20',
            'password'   => 'required|string|min:8',
            'status'     => 'required',
            'role_id'    => 'required|exists:roles,id',
        ]);

        $admin = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => $request->password,
            'status'     => $request->status,
            'role'       => 'admin',
        ]);

        $role = Role::findById($request->role_id, 'web');
        $admin->assignRole($role);

        return redirect()->route('admin-users.index')
            ->with('success', __('Admin created successfully.'));
    }

    public function edit(User $adminUser)
    {
        $statuses = ActivationStatus::labels();
        $roles = Role::orderBy('name')->get();
        $adminRole = $adminUser->roles->first();
        return view('admin-users.edit', compact('adminUser', 'statuses', 'roles', 'adminRole'));
    }

    public function update(Request $request, User $adminUser)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $adminUser->id,
            'phone'      => 'required|string|max:20',
            'status'     => 'required',
            'role_id'    => 'required|exists:roles,id',
        ]);

        $adminUser->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'status'     => $request->status,
        ]);

        if ($request->filled('password')) {
            $adminUser->update(['password' => $request->password]);
        }

        $role = Role::findById($request->role_id, 'web');
        $adminUser->syncRoles([$role]);

        return redirect()->route('admin-users.index')
            ->with('success', __('Admin updated successfully.'));
    }

    public function destroy(User $adminUser)
    {
        if (auth()->id() === $adminUser->id) {
            return redirect()->route('admin-users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        $adminUser->delete();
        return redirect()->route('admin-users.index')
            ->with('success', __('Admin deleted successfully.'));
    }
}
