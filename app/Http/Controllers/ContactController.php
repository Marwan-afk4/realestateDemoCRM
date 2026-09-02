<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Enums\ContactSource;
use App\Enums\MessageChannel;
use App\Models\Brocker;
use App\Models\Contact;
use App\Models\MessageTemplate;
use App\Models\UptownType;
use App\Services\Crm\ActivityLogger;
use App\Services\Crm\PipelineService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function __construct(
        private SalesVisibility $visibility,
        private ActivityLogger $activities,
        private PipelineService $pipeline,
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

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $this->authorize('create', Contact::class);

        return view('contacts.create', $this->formData());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Contact::class);

        $data = $this->validated($request);
        $data['owner_id'] = $data['owner_id'] ?? $request->user()->id;
        $data['tags'] = $this->parseTags($request->input('tags'));

        if (! empty($data['phone']) && Contact::findByPhone($data['phone'])) {
            return back()->withInput()->withErrors(['phone' => __('A contact with this phone already exists.')]);
        }

        $contact = Contact::create($data);

        $this->activities->log($contact, ActivityType::System, __('Contact created'));

        if ($request->boolean('add_to_pipeline')) {
            $this->pipeline->openLeadForContact($contact, $request->user());

            return redirect()->route('pipeline.index')->with('success', __('Contact added to the pipeline.'));
        }

        return redirect()->route('contacts.show', $contact)->with('success', __('Created successfully'));
    }

    public function addToPipeline(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $this->pipeline->openLeadForContact($contact, $request->user());

        return redirect()->route('pipeline.index')->with('success', __('Contact added to the pipeline.'));
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

        $activities = $contact->activities()->with('user')->limit(50)->get();
        $tasks = $contact->tasks()->with('owner')->latest('due_at')->get();
        $templates = MessageTemplate::query()->where('is_active', true)->orderBy('name')->get();
        $brokers = Brocker::with('user')->get();
        $inventoryUnits = \App\Models\InventoryUnit::query()
            ->with('compound')
            ->whereNotIn('status', [\App\Enums\InventoryStatus::Sold->value, \App\Enums\InventoryStatus::HandedOver->value])
            ->orderBy('code')
            ->get()
            ->mapWithKeys(fn ($unit) => [$unit->id => $unit->code.' — '.$unit->address().' ('.$unit->status->label().')']);

        return view('contacts.show', compact('contact', 'activities', 'tasks', 'templates', 'brokers', 'inventoryUnits'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);

        return view('contacts.edit', array_merge($this->formData(), compact('contact')));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('update', $contact);

        $data = $this->validated($request, $contact);
        $data['tags'] = $this->parseTags($request->input('tags'));

        if (! empty($data['phone'])) {
            $duplicate = Contact::findByPhone($data['phone']);
            if ($duplicate && $duplicate->id !== $contact->id) {
                return back()->withInput()->withErrors(['phone' => __('A contact with this phone already exists.')]);
            }
        }

        $contact->update($data);

        return redirect()->route('contacts.show', $contact)->with('success', __('Updated successfully.'));
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
        $this->activities->log(
            $contact,
            $type,
            $type->label(),
            $data['body'] ?? null,
            [],
            $contact->tickets()->find($data['pipeline_ticket_id'] ?? 0),
        );

        return back()->with('success', __('Activity logged.'));
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
        $type = match ($channel) {
            MessageChannel::Whatsapp => ActivityType::Whatsapp,
            MessageChannel::Sms => ActivityType::Sms,
            MessageChannel::Email => ActivityType::Email,
        };

        $this->activities->log(
            $contact,
            $type,
            $channel->label(),
            $data['body'],
            ['subject' => $data['subject'] ?? null, 'template_id' => $data['template_id'] ?? null],
        );

        $redirect = match ($channel) {
            MessageChannel::Whatsapp => $contact->whatsappLink()
                ? $contact->whatsappLink().'?text='.rawurlencode($data['body'])
                : null,
            MessageChannel::Email => $contact->email
                ? 'mailto:'.$contact->email.'?subject='.rawurlencode($data['subject'] ?? '').'&body='.rawurlencode($data['body'])
                : null,
            MessageChannel::Sms => $contact->telLink()
                ? 'sms:'.ltrim((string) $contact->phone_e164, '+').'?body='.rawurlencode($data['body'])
                : null,
        };

        if ($redirect) {
            return redirect()->away($redirect);
        }

        return back()->with('success', __('Message logged.'));
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

    private function formData(): array
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';

        return [
            'sources' => ContactSource::labels(),
            'uptownTypes' => UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray(),
        ];
    }
}
