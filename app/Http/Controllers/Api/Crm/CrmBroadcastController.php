<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\MessageChannel;
use App\Enums\PipelineStage;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmBroadcast;
use App\Models\MessageTemplate;
use App\Services\Crm\MessagingService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmBroadcastController extends Controller
{
    use RespondsJson;

    public function __construct(
        private SalesVisibility $visibility,
        private MessagingService $messaging,
    ) {
    }

    public function index()
    {
        $this->authorizeView();
        $broadcasts = CrmBroadcast::with(['sender', 'template'])->latest()->paginate(20);

        return $this->paginated($broadcasts, fn (CrmBroadcast $broadcast) => [
            'id' => $broadcast->id,
            'title' => $broadcast->title,
            'channel' => $broadcast->channel?->value,
            'recipient_count' => $broadcast->recipient_count,
            'filters' => $broadcast->filters,
            'sent_at' => $broadcast->sent_at?->toIso8601String(),
            'sender' => $this->userSummary($broadcast->sender),
            'template' => $broadcast->template?->only(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeView();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'channel' => ['required', Rule::enum(MessageChannel::class)],
            'message_template_id' => 'nullable|exists:message_templates,id',
            'body' => 'required|string',
            'stage' => ['nullable', Rule::enum(PipelineStage::class)],
            'source' => 'nullable|string',
        ]);

        $contacts = $this->resolveRecipients($data);
        $channel = MessageChannel::from($data['channel']);

        foreach ($contacts as $contact) {
            $this->messaging->prepareOutbound(
                $contact,
                $channel,
                $data['body'],
                $request->user(),
                null,
                $data['title'],
                ['broadcast' => true],
            );
        }

        $broadcast = CrmBroadcast::create([
            'title' => $data['title'],
            'channel' => $channel,
            'message_template_id' => $data['message_template_id'] ?? null,
            'body' => $data['body'],
            'filters' => ['stage' => $data['stage'] ?? null, 'source' => $data['source'] ?? null],
            'recipient_count' => $contacts->count(),
            'sent_by' => $request->user()->id,
            'sent_at' => now(),
        ]);

        return $this->created($this->showPayload($broadcast), __('Broadcast logged to :count contacts.', ['count' => $contacts->count()]));
    }

    public function show(CrmBroadcast $crm_broadcast)
    {
        $this->authorizeView();

        return $this->ok($this->showPayload($crm_broadcast));
    }

    private function showPayload(CrmBroadcast $broadcast): array
    {
        $contacts = $this->resolveRecipients($broadcast->filters ?? []);

        return [
            'id' => $broadcast->id,
            'title' => $broadcast->title,
            'channel' => $broadcast->channel?->value,
            'body' => $broadcast->body,
            'filters' => $broadcast->filters,
            'recipient_count' => $broadcast->recipient_count,
            'sent_at' => $broadcast->sent_at?->toIso8601String(),
            'recipients' => $contacts->map(fn (Contact $contact) => [
                'id' => $contact->id,
                'name' => $contact->name,
                'phone' => $contact->phone,
                'link' => $this->messaging->deepLink($contact, $broadcast->channel, $broadcast->body),
            ]),
        ];
    }

    private function resolveRecipients(array $filters)
    {
        return $this->visibility->scopeContacts(Contact::query(), auth()->user())
            ->when($filters['source'] ?? null, fn ($q, $source) => $q->where('source', $source))
            ->when($filters['stage'] ?? null, function ($q, $stage) {
                $q->whereHas('tickets', fn ($tickets) => $tickets->where('stage', $stage));
            })
            ->get();
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()?->can('view-message-templates') || auth()->user()?->can('view-pipeline'), 403);
    }
}
