<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Enums\MessageChannel;
use App\Enums\PipelineStage;
use App\Models\Contact;
use App\Models\CrmBroadcast;
use App\Models\MessageTemplate;
use App\Services\Crm\ActivityLogger;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmBroadcastController extends Controller
{
    public function __construct(private SalesVisibility $visibility, private ActivityLogger $activities)
    {
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

        $contacts = $this->visibility->scopeContacts(Contact::query(), $request->user())
            ->when($data['source'] ?? null, fn ($q, $source) => $q->where('source', $source))
            ->when($data['stage'] ?? null, function ($q, $stage) {
                $q->whereHas('tickets', fn ($tickets) => $tickets->where('stage', $stage));
            })
            ->get();

        $channel = MessageChannel::from($data['channel']);
        $type = match ($channel) {
            MessageChannel::Whatsapp => ActivityType::Whatsapp,
            MessageChannel::Sms => ActivityType::Sms,
            MessageChannel::Email => ActivityType::Email,
        };

        foreach ($contacts as $contact) {
            $this->activities->log($contact, $type, $data['title'], $data['body'], ['broadcast' => true]);
        }

        CrmBroadcast::create([
            'title' => $data['title'],
            'channel' => $channel,
            'message_template_id' => $data['message_template_id'] ?? null,
            'body' => $data['body'],
            'filters' => ['stage' => $data['stage'] ?? null, 'source' => $data['source'] ?? null],
            'recipient_count' => $contacts->count(),
            'sent_by' => $request->user()->id,
            'sent_at' => now(),
        ]);

        return redirect()->route('crm-broadcasts.index')
            ->with('success', __('Broadcast logged to :count contacts.', ['count' => $contacts->count()]));
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()?->can('view-message-templates') || auth()->user()?->can('view-pipeline'), 403);
    }
}
