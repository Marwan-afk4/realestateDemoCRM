<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MarketingAgency;
use App\Models\PipelineTicket;
use App\Models\User;
use App\Enums\PipelineStage;
use App\Services\Crm\AgencyVisibility;
use App\Services\Crm\UnitMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AgencyWorkspaceController extends Controller
{
    public function __construct(
        private AgencyVisibility $visibility,
        private UnitMatchingService $matching,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-agency-workspace'), 403);

        $agency = $this->resolveAgency($request);
        abort_unless($agency, 404);

        $leadQuery = $this->visibility->scopeLeads(Lead::query(), $request->user());
        $stats = [
            'agents' => User::query()->where('marketing_agency_id', $agency->id)->where('role', 'agency')->count(),
            'leads' => (clone $leadQuery)->count(),
            'open_tickets' => PipelineTicket::query()
                ->whereHasMorph('ticketable', [Lead::class], fn ($q) => $q->where('marketing_agency_id', $agency->id))
                ->whereNotIn('stage', [PipelineStage::Won, PipelineStage::Lost])
                ->count(),
        ];

        $recentLeads = (clone $leadQuery)->with(['brocker.user', 'ticket'])->latest()->limit(10)->get();
        $agents = User::query()->where('marketing_agency_id', $agency->id)->where('role', 'agency')->orderBy('first_name')->get();

        return view('agency.workspace', compact('agency', 'stats', 'recentLeads', 'agents'));
    }

    public function matching(Request $request)
    {
        abort_unless($request->user()->can('view-unit-matching'), 403);

        $contact = null;
        $matches = collect();

        if ($request->filled('contact_id')) {
            $contact = \App\Models\Contact::query()->findOrFail($request->contact_id);
            $matches = $this->matching->matchForContact($contact);
        }

        $contacts = \App\Models\Contact::query()
            ->when($request->user()->role === 'agency', function ($q) use ($request) {
                $agencyId = $request->user()->marketing_agency_id;
                if ($agencyId) {
                    $q->whereHas('leads', fn ($leads) => $leads->where('marketing_agency_id', $agencyId));
                }
            })
            ->orderBy('name')
            ->limit(200)
            ->pluck('name', 'id');

        return view('agency.matching', compact('contact', 'matches', 'contacts'));
    }

    public function storeAgent(Request $request, MarketingAgency $marketingAgency)
    {
        abort_unless($request->user()->can('view-marketing-agencies') || $request->user()->can('view-agency-workspace'), 403);
        $this->authorizeAgency($request, $marketingAgency);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            ...$data,
            'role' => 'agency',
            'marketing_agency_id' => $marketingAgency->id,
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::findByName('agency-manager', 'web');
        $user->assignRole($role);

        return back()->with('success', __('Agency agent :name created.', ['name' => $user->full_name]));
    }

    private function resolveAgency(Request $request): ?MarketingAgency
    {
        if ($request->user()->role === 'agency' && $request->user()->marketing_agency_id) {
            return MarketingAgency::query()->find($request->user()->marketing_agency_id);
        }

        if ($request->user()->role === 'admin' && $request->filled('agency_id')) {
            return MarketingAgency::query()->find($request->agency_id);
        }

        return MarketingAgency::query()->orderBy('name')->first();
    }

    private function authorizeAgency(Request $request, MarketingAgency $agency): void
    {
        if ($request->user()->role === 'agency' && $request->user()->marketing_agency_id !== $agency->id) {
            abort(403);
        }
    }
}
