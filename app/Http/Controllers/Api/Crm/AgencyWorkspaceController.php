<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\PipelineStage;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\MarketingAgency;
use App\Models\PipelineTicket;
use App\Models\User;
use App\Services\Crm\AgencyVisibility;
use App\Services\Crm\UnitMatchingService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AgencyWorkspaceController extends Controller
{
    use RespondsJson;

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

        $recentLeads = (clone $leadQuery)->with(['brocker.user', 'ticket'])->latest()->limit(10)->get()
            ->map(fn (Lead $lead) => [
                'id' => $lead->id,
                'name' => $lead->lead_name,
                'phone' => $lead->lead_phone,
                'status' => $lead->status?->value ?? $lead->status,
                'broker' => $lead->brocker?->user?->full_name,
                'ticket_id' => $lead->ticket?->id,
            ]);

        $agents = User::query()->where('marketing_agency_id', $agency->id)->where('role', 'agency')->orderBy('first_name')->get()
            ->map(fn (User $user) => $this->userSummary($user));

        return $this->ok([
            'agency' => $agency->only(['id', 'name', 'email', 'phone']),
            'stats' => $stats,
            'recent_leads' => $recentLeads,
            'agents' => $agents,
        ]);
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

        return $this->ok([
            'contact' => $contact ? ['id' => $contact->id, 'name' => $contact->name, 'phone' => $contact->phone] : null,
            'matches' => $matches->map(fn ($row) => [
                'score' => $row['score'],
                'price' => $row['price'],
                'unit' => [
                    'id' => $row['unit']->id,
                    'code' => $row['unit']->code,
                    'address' => $row['unit']->address(),
                    'status' => $row['unit']->status?->value,
                ],
            ]),
        ]);
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
        ]);

        $role = Role::findByName('agency-manager', 'web');
        $user->assignRole($role);

        return $this->created($this->userSummary($user), __('Agency agent :name created.', ['name' => $user->full_name]));
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
