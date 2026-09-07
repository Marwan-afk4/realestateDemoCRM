<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\CrmTaskType;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmTask;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmTaskController extends Controller
{
    use RespondsJson;

    public function __construct(private SalesVisibility $visibility)
    {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-crm-tasks') || $request->user()->can('view-pipeline'), 403);

        $tasks = $this->visibility->scopeTasks(CrmTask::query()->with(['contact', 'owner', 'ticket']), $request->user())
            ->when($request->filter === 'open', fn ($q) => $q->whereNull('completed_at'))
            ->when($request->filter === 'overdue', fn ($q) => $q->whereNull('completed_at')->where('due_at', '<', now()))
            ->when($request->filter === 'today', fn ($q) => $q->whereDate('due_at', today()))
            ->orderByRaw('completed_at is null desc')
            ->orderBy('due_at')
            ->paginate(40);

        return $this->paginated($tasks, fn (CrmTask $task) => $this->serialize($task));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->can('view-crm-tasks') || $request->user()->can('view-pipeline'), 403);

        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'pipeline_ticket_id' => 'nullable|exists:pipeline_tickets,id',
            'type' => ['required', Rule::enum(CrmTaskType::class)],
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'due_at' => 'required|date',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $contact = Contact::findOrFail($data['contact_id']);
        $this->authorize('update', $contact);

        $data['created_by'] = $request->user()->id;
        $data['owner_id'] = $data['owner_id'] ?? $request->user()->id;

        $task = CrmTask::create($data);

        return $this->created($this->serialize($task->load(['contact', 'owner'])), __('Task created.'));
    }

    public function complete(CrmTask $crm_task)
    {
        $this->authorize('update', $crm_task->contact);
        $crm_task->update(['completed_at' => now()]);

        return $this->ok($this->serialize($crm_task->fresh(['contact', 'owner'])), __('Task completed.'));
    }

    public function calendarEvents(Request $request)
    {
        abort_unless($request->user()->can('view-crm-tasks') || $request->user()->can('view-pipeline'), 403);

        $start = $request->date('start') ?? now()->startOfMonth();
        $end = $request->date('end') ?? now()->endOfMonth()->addMonth();

        $tasks = $this->visibility->scopeTasks(
            CrmTask::query()->with('contact'),
            $request->user(),
        )
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [$start, $end])
            ->get();

        return $this->ok($tasks->map(fn (CrmTask $task) => [
            'id' => $task->id,
            'title' => trim(($task->contact?->name ?? __('Contact')).': '.$task->title),
            'start' => $task->due_at->toIso8601String(),
            'contact_id' => $task->contact_id,
            'completed' => (bool) $task->completed_at,
            'overdue' => $task->isOverdue(),
            'type' => $task->type?->value,
            'type_label' => $task->type?->label(),
        ]));
    }

    private function serialize(CrmTask $task): array
    {
        return [
            'id' => $task->id,
            'type' => $task->type?->value,
            'type_label' => $task->type?->label(),
            'title' => $task->title,
            'body' => $task->body,
            'due_at' => $task->due_at?->toIso8601String(),
            'completed_at' => $task->completed_at?->toIso8601String(),
            'overdue' => $task->isOverdue(),
            'contact' => $task->contact ? [
                'id' => $task->contact->id,
                'name' => $task->contact->name,
                'phone' => $task->contact->phone,
            ] : null,
            'owner' => $this->userSummary($task->owner),
            'pipeline_ticket_id' => $task->pipeline_ticket_id,
        ];
    }
}
