<?php

namespace App\Http\Controllers;

use App\Enums\CrmTaskType;
use App\Models\Contact;
use App\Models\CrmTask;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmTaskController extends Controller
{
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

        $calendarTasks = $this->visibility->scopeTasks(CrmTask::query()->with('contact'), $request->user())
            ->whereNotNull('due_at')
            ->whereNull('completed_at')
            ->whereBetween('due_at', [now()->startOfMonth(), now()->endOfMonth()->addMonth()])
            ->get();

        return view('crm-tasks.index', compact('tasks', 'calendarTasks'));
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

        CrmTask::create($data);

        return back()->with('success', __('Task created.'));
    }

    public function complete(CrmTask $crm_task)
    {
        $this->authorize('update', $crm_task->contact);
        $crm_task->update(['completed_at' => now()]);

        return back()->with('success', __('Task completed.'));
    }
}
