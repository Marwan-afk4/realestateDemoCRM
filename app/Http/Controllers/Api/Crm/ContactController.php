<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\ActivityType;
use App\Enums\ContactSource;
use App\Enums\MessageChannel;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\InventoryUnit;
use App\Models\MessageTemplate;
use App\Services\Crm\ActivityLogger;
use App\Services\Crm\MessagingService;
use App\Services\Crm\PipelineService;
use App\Services\Crm\SalesVisibility;
use App\Services\Crm\UnitMatchingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    use RespondsJson;

    public function __construct(
        private SalesVisibility $visibility,
        private ActivityLogger $activities,
        private PipelineService $pipeline,
        private MessagingService $messaging,
        private UnitMatchingService $unitMatching,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);

        $contacts = $this->visibility->scopeContacts(Contact::query(), $request->user())
            ->with(['owner', 'uptownType'])
            ->withCount(['tickets', 'deals'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhere('phone_e164', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('whatsapp', 'like', "%{$keyword}%");
                });
            })
            ->when($request->source, fn ($q, $source) => $q->where('source', $source))
            ->latest()
            ->paginate(30);

        return $this->paginated($contacts, fn (Contact $contact) => $this->summary($contact));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Contact::class);

        $data = $this->validated($request);
        $data['owner_id'] = $data['owner_id'] ?? $request->user()->id;
        $data['tags'] = $this->parseTags($request->input('tags'));

        if (! empty($data['phone']) && Contact::findByPhone($data['phone'])) {
            return response()->json([
                'status' => 'error',
                'message' => __('A contact with this phone already exists.'),
                'errors' => ['phone' => [__('A contact with this phone already exists.')]],
            ], 422);
        }

        $contact = Contact::create($data);
        $this->activities->log($contact, ActivityType::System, __('Contact created'));

        $ticket = null;
        if ($request->boolean('add_to_pipeline')) {
            $ticket = $this->pipeline->openLeadForContact($contact, $request->user());
        }

        return $this->created([
            'contact' => $this->detail($contact->fresh(['owner', 'uptownType'])),
            'ticket' => $ticket ? $this->ticketSummary($ticket) : null,
        ], __('Created successfully'));
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        $contact->load([
            'owner',
            'uptownType',
            'tickets.owner',
            'tickets.brocker.user',
            'tickets.inventoryUnit',
            'deals.uptown',
            'leads',
            'sellRequests',
            'mortgageRequests',
        ]);

        $payload = $this->detail($contact);
        $payload['activities'] = $contact->activities()->with('user')->limit(50)->get()->map(fn ($activity) => [
            'id' => $activity->id,
            'type' => $activity->type?->value,
            'type_label' => $activity->type?->label(),
            'title' => $activity->title,
            'body' => $activity->body,
            'meta' => $activity->meta,
            'user' => $this->userSummary($activity->user),
            'pipeline_ticket_id' => $activity->pipeline_ticket_id,
            'created_at' => $activity->created_at?->toIso8601String(),
        ]);
        $payload['tasks'] = $contact->tasks()->with('owner')->latest('due_at')->get()->map(fn ($task) => [
            'id' => $task->id,
            'type' => $task->type?->value,
            'type_label' => $task->type?->label(),
            'title' => $task->title,
            'body' => $task->body,
            'due_at' => $task->due_at?->toIso8601String(),
            'completed_at' => $task->completed_at?->toIso8601String(),
            'overdue' => $task->isOverdue(),
            'owner' => $this->userSummary($task->owner),
            'pipeline_ticket_id' => $task->pipeline_ticket_id,
        ]);
        $payload['tickets'] = $contact->tickets->map(fn ($ticket) => $this->ticketSummary($ticket));
        $payload['deals'] = $contact->deals->map(fn ($deal) => [
            'id' => $deal->id,
            'status' => $deal->status?->value ?? $deal->status,
            'value' => $deal->value,
            'uptown' => $deal->uptown?->name_en ?? $deal->uptown?->name_ar,
        ]);
        $payload['templates'] = MessageTemplate::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'channel', 'subject', 'body']);
        $payload['whatsapp_link'] = $contact->whatsappLink();
        $payload['tel_link'] = $contact->telLink();

        if (auth()->user()?->can('view-unit-matching')) {
            $payload['unit_matches'] = $this->unitMatching->matchForContact($contact, 10)->map(fn ($row) => [
                'score' => $row['score'],
                'price' => $row['price'],
                'unit' => $this->unitSummary($row['unit']),
            ]);
        }

        return $this->ok($payload);
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $data = $this->validated($request, $contact);
        $data['tags'] = $this->parseTags($request->input('tags'));

        if (! empty($data['phone'])) {
            $duplicate = Contact::findByPhone($data['phone']);
            if ($duplicate && $duplicate->id !== $contact->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('A contact with this phone already exists.'),
                    'errors' => ['phone' => [__('A contact with this phone already exists.')]],
                ], 422);
            }
        }

        $contact->update($data);

        return $this->ok($this->detail($contact->fresh(['owner', 'uptownType'])), __('Updated successfully.'));
    }

    public function addToPipeline(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $ticket = $this->pipeline->openLeadForContact($contact, $request->user());

        return $this->ok($this->ticketSummary($ticket), __('Contact added to the pipeline.'));
    }

    public function logActivity(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $data = $request->validate([
            'type' => ['required', Rule::enum(ActivityType::class)],
            'body' => 'nullable|string',
            'pipeline_ticket_id' => 'nullable|exists:pipeline_tickets,id',
        ]);

        $type = ActivityType::from($data['type']);
        $activity = $this->activities->log(
            $contact,
            $type,
            $type->label(),
            $data['body'] ?? null,
            [],
            $contact->tickets()->find($data['pipeline_ticket_id'] ?? 0),
        );

        return $this->ok([
            'id' => $activity->id,
            'type' => $activity->type?->value,
            'title' => $activity->title,
            'body' => $activity->body,
            'created_at' => $activity->created_at?->toIso8601String(),
        ], __('Activity logged.'));
    }

    public function sendMessage(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $data = $request->validate([
            'template_id' => 'nullable|exists:message_templates,id',
            'channel' => ['required', Rule::enum(MessageChannel::class)],
            'body' => 'required|string',
            'subject' => 'nullable|string',
        ]);

        $channel = MessageChannel::from($data['channel']);
        $result = $this->messaging->prepareOutbound(
            $contact,
            $channel,
            $data['body'],
            $request->user(),
            $data['subject'] ?? null,
            $channel->label(),
            ['template_id' => $data['template_id'] ?? null],
        );

        return $this->ok([
            'channel' => $channel->value,
            'link' => $result['link'],
        ], __('Message logged.'));
    }

    public function logInbound(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $data = $request->validate([
            'channel' => ['required', Rule::enum(MessageChannel::class)],
            'body' => 'required|string',
            'subject' => 'nullable|string',
        ]);

        $this->messaging->logInbound(
            $contact,
            MessageChannel::from($data['channel']),
            $data['body'],
            $request->user(),
            $data['subject'] ?? null,
        );

        return $this->ok(null, __('Inbound message logged on timeline.'));
    }

    public function matches(Contact $contact)
    {
        $this->authorize('view', $contact);
        abort_unless(auth()->user()?->can('view-unit-matching') || auth()->user()?->can('view-pipeline'), 403);

        $matches = $this->unitMatching->matchForContact($contact, 25)->map(fn ($row) => [
            'score' => $row['score'],
            'price' => $row['price'],
            'unit' => $this->unitSummary($row['unit']),
        ]);

        return $this->ok($matches);
    }

    private function validated(Request $request, ?Contact $contact = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'phone_secondary' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'national_id' => 'nullable|string|max:50',
            'source' => ['required', Rule::enum(ContactSource::class)],
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'preferred_area' => 'nullable|string|max:255',
            'uptown_type_id' => 'nullable|exists:uptown_types,id',
            'intent' => 'nullable|in:buy,rent,sell,mortgage',
            'payment_preference' => 'nullable|in:cash,installment',
            'owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
    }

    private function parseTags($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    private function summary(Contact $contact): array
    {
        return [
            'id' => $contact->id,
            'name' => $contact->name,
            'phone' => $contact->phone,
            'email' => $contact->email,
            'whatsapp' => $contact->whatsapp,
            'source' => $contact->source?->value,
            'source_label' => $contact->source?->label(),
            'preferred_area' => $contact->preferred_area,
            'budget_min' => $contact->budget_min,
            'budget_max' => $contact->budget_max,
            'intent' => $contact->intent,
            'tags' => $contact->tagsList(),
            'owner' => $this->userSummary($contact->owner),
            'uptown_type' => $contact->uptownType?->only(['id', 'name_en', 'name_ar']),
            'tickets_count' => $contact->tickets_count ?? $contact->tickets()->count(),
            'deals_count' => $contact->deals_count ?? $contact->deals()->count(),
            'last_contacted_at' => $contact->last_contacted_at?->toIso8601String(),
            'created_at' => $contact->created_at?->toIso8601String(),
        ];
    }

    private function detail(Contact $contact): array
    {
        return array_merge($this->summary($contact), [
            'phone_e164' => $contact->phone_e164,
            'phone_secondary' => $contact->phone_secondary,
            'national_id' => $contact->national_id,
            'payment_preference' => $contact->payment_preference,
            'notes' => $contact->notes,
            'initials' => $contact->initials(),
        ]);
    }

    private function ticketSummary($ticket): array
    {
        return [
            'id' => $ticket->id,
            'type' => $ticket->type?->value,
            'type_label' => $ticket->type?->label(),
            'stage' => $ticket->stage?->value,
            'stage_label' => $ticket->stage?->label(),
            'probability' => $ticket->probability,
            'locked' => $ticket->isLocked(),
            'owner' => $this->userSummary($ticket->owner),
            'broker' => $ticket->brocker ? [
                'id' => $ticket->brocker->id,
                'name' => $ticket->brocker->user?->full_name,
            ] : null,
            'inventory_unit' => $ticket->inventoryUnit ? $this->unitSummary($ticket->inventoryUnit) : null,
            'updated_at' => $ticket->updated_at?->toIso8601String(),
        ];
    }

    private function unitSummary(InventoryUnit $unit): array
    {
        return [
            'id' => $unit->id,
            'code' => $unit->code,
            'address' => $unit->address(),
            'status' => $unit->status?->value,
            'status_label' => $unit->status?->label(),
            'price' => $unit->price(),
            'compound' => $unit->compound?->compound_name,
        ];
    }
}
