<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin-roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('admin-roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|unique:roles,name|max:100',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin-roles.index')
            ->with('success', __('Role created successfully.'));
    }

    public function edit(Role $adminRole)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $adminRole->permissions->pluck('id')->toArray();
        return view('admin-roles.edit', compact('adminRole', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $adminRole)
    {
        $request->validate([
            'name'          => 'required|string|max:100|unique:roles,name,' . $adminRole->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $adminRole->update(['name' => $request->name]);
        
        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $adminRole->syncPermissions($permissions);

        return redirect()->route('admin-roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $adminRole)
    {
        if ($adminRole->name === 'super-admin') {
            return redirect()->route('admin-roles.index')
                ->with('error', __('Cannot delete the super-admin role.'));
        }

        $adminRole->delete();
        return redirect()->route('admin-roles.index')
            ->with('success', __('Role deleted successfully.'));
    }
}
