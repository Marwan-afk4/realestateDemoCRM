<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->homeRoute(Auth::user()))->with('success', 'You are already logged in');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['error' => 'Mobile number or password is incorrect'])->withInput();
        }

        if (! in_array($user->role, ['admin', 'brocker', 'agency', 'developer'], true)) {
            return back()->withErrors(['error' => 'You are not authorized to access this area'])->withInput();
        }

        $this->ensureDefaultRole($user);

        if (! $this->hasPanelAccess($user)) {
            return back()->withErrors(['error' => __('Your account has no workspace permissions. Contact an administrator.')])->withInput();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route($this->homeRoute($user)))->with('success', 'Logged in successfully');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->forceFill(['remember_token' => null])->save();
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect()->route('login')->with('message', 'Logged out successfully');
    }

    private function homeRoute(User $user): string
    {
        return match ($user->role) {
            'brocker' => 'pipeline.index',
            'agency' => 'agency.workspace',
            'developer' => 'developer-portal.index',
            default => 'home',
        };
    }

    private function ensureDefaultRole(User $user): void
    {
        $roleName = match ($user->role) {
            'brocker' => 'broker',
            'agency' => 'agency-manager',
            'developer' => 'developer-admin',
            default => null,
        };

        if (! $roleName) {
            return;
        }

        $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
        if ($role && ! $user->hasRole($role)) {
            $user->assignRole($role);
        }
    }

    private function hasPanelAccess(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        $permissions = [
            'view-contacts', 'view-pipeline', 'view-crm-tasks', 'view-crm-reports',
            'view-deals', 'view-inventory', 'view-collections', 'view-leads',
            'view-agency-workspace', 'view-developer-portal', 'view-after-sales',
            'view-marketing-agencies', 'view-unit-matching', 'manage-developer-brokers',
        ];

        return collect($permissions)->contains(fn ($permission) => $user->can($permission));
    }
}
