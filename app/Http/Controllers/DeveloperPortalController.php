<?php

namespace App\Http\Controllers;

use App\Enums\InventoryStatus;
use App\Models\Brocker;
use App\Models\Compound;
use App\Models\Deal;
use App\Models\Developer;
use App\Models\InventoryUnit;
use App\Models\User;
use App\Services\Crm\DeveloperVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DeveloperPortalController extends Controller
{
    public function __construct(private DeveloperVisibility $visibility)
    {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-developer-portal'), 403);

        $developer = $this->resolveDeveloper($request);
        abort_unless($developer, 404);

        $inventoryQuery = $this->visibility->scopeInventory(InventoryUnit::query(), $request->user());
        $stats = [
            'compounds' => Compound::query()->where('developer_id', $developer->id)->count(),
            'available' => (clone $inventoryQuery)->where('status', InventoryStatus::Available)->count(),
            'reserved' => (clone $inventoryQuery)->whereIn('status', [InventoryStatus::Held, InventoryStatus::Reserved, InventoryStatus::Contracted])->count(),
            'sold' => (clone $inventoryQuery)->whereIn('status', [InventoryStatus::Sold, InventoryStatus::HandedOver])->count(),
            'deals' => $this->visibility->scopeDeals(Deal::query(), $request->user())->count(),
            'brokers' => $developer->authorizedBrokers()->count(),
        ];

        return view('developer-portal.index', compact('developer', 'stats'));
    }

    public function inventory(Request $request)
    {
        abort_unless($request->user()->can('view-developer-portal'), 403);

        $developer = $this->resolveDeveloper($request);
        abort_unless($developer, 404);

        $units = $this->visibility->scopeInventory(
            InventoryUnit::query()->with(['compound', 'activeDeal.contact']),
            $request->user(),
        )->orderBy('code')->paginate(40);

        return view('developer-portal.inventory', compact('developer', 'units'));
    }

    public function brokers(Request $request)
    {
        abort_unless($request->user()->can('view-developer-portal'), 403);

        $developer = $this->resolveDeveloper($request);
        abort_unless($developer, 404);

        $authorized = $developer->authorizedBrokers()->with('user')->get()->pluck('id')->all();
        $brokers = Brocker::with('user')->get()->mapWithKeys(fn ($b) => [$b->id => $b->user?->full_name ?? '#'.$b->id])->toArray();

        return view('developer-portal.brokers', compact('developer', 'authorized', 'brokers'));
    }

    public function syncBrokers(Request $request)
    {
        abort_unless($request->user()->can('manage-developer-brokers'), 403);

        $developer = $this->resolveDeveloper($request);
        abort_unless($developer, 404);

        $data = $request->validate([
            'brocker_ids' => 'nullable|array',
            'brocker_ids.*' => 'exists:brockers,id',
        ]);

        $this->visibility->syncAuthorizedBrokers($developer, $data['brocker_ids'] ?? []);

        return back()->with('success', __('Authorized brokers updated.'));
    }

    public function storePortalUser(Request $request, Developer $developer)
    {
        abort_unless($request->user()->role === 'admin' || ($request->user()->developer_id === $developer->id), 403);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            ...$data,
            'role' => 'developer',
            'developer_id' => $developer->id,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole(Role::findByName('developer-admin', 'web'));

        return back()->with('success', __('Developer portal user created.'));
    }

    private function resolveDeveloper(Request $request): ?Developer
    {
        if ($request->user()->role === 'developer' && $request->user()->developer_id) {
            return Developer::query()->find($request->user()->developer_id);
        }

        if ($request->user()->role === 'admin') {
            $id = $request->get('developer_id', Developer::query()->orderBy('name_en')->value('id'));

            return $id ? Developer::query()->find($id) : null;
        }

        return null;
    }
}
