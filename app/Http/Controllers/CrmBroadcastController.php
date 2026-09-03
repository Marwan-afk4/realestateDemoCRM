<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Enums\MessageChannel;
use App\Enums\PipelineStage;
use App\Models\Contact;
use App\Models\CrmBroadcast;
use App\Models\MessageTemplate;
use App\Services\Crm\MessagingService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmBroadcastController extends Controller
{
    public function __construct(
        private SalesVisibility $visibility,
        private MessagingService $messaging,
    ) {
    }

    public function index()
    {
        $this->authorizeView();
        $broadcasts = CrmBroadcast::with(['sender', 'template'])->latest()->paginate(20);

        return view('crm-broadcasts.index', compact('broadcasts'));
    }

    public function create()
    {
        $this->authorizeView();

        return view('crm-broadcasts.create', [
            'templates' => MessageTemplate::where('is_active', true)->orderBy('name')->get(),
            'channels' => MessageChannel::labels(),
            'stages' => PipelineStage::labels(),
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

        return redirect()->route('crm-broadcasts.show', $broadcast)
            ->with('success', __('Broadcast logged to :count contacts. Open each link below.', ['count' => $contacts->count()]));
    }

    public function show(CrmBroadcast $crm_broadcast)
    {
        $this->authorizeView();

        $contacts = $this->resolveRecipients($crm_broadcast->filters ?? []);
        $recipients = $contacts->map(fn (Contact $contact) => [
            'contact' => $contact,
            'link' => $this->messaging->deepLink($contact, $crm_broadcast->channel, $crm_broadcast->body),
        ]);

        return view('crm-broadcasts.show', [
            'broadcast' => $crm_broadcast,
            'recipients' => $recipients,
        ]);
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
