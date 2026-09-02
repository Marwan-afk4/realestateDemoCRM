<?php

namespace App\Http\Controllers;

use App\Enums\MessageChannel;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $this->authorizeView();
        $templates = MessageTemplate::query()->latest()->paginate(30);

        return view('message-templates.index', compact('templates'));
    }

    public function create()
    {
        $this->authorizeView();

        return view('message-templates.create', ['channels' => MessageChannel::labels()]);
    }

    public function store(Request $request)
    {
        $this->authorizeView();
        MessageTemplate::create($this->validated($request));

        return redirect()->route('message-templates.index')->with('success', __('Created successfully'));
    }

    public function edit(MessageTemplate $message_template)
    {
        $this->authorizeView();

        return view('message-templates.edit', [
            'template' => $message_template,
            'channels' => MessageChannel::labels(),
        ]);
    }

    public function update(Request $request, MessageTemplate $message_template)
    {
        $this->authorizeView();
        $message_template->update($this->validated($request));

        return redirect()->route('message-templates.index')->with('success', __('Updated successfully.'));
    }

    public function destroy(MessageTemplate $message_template)
    {
        $this->authorizeView();
        $message_template->delete();

        return back()->with('success', __('Deleted successfully'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'channel' => ['required', Rule::enum(MessageChannel::class)],
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()?->can('view-message-templates') || auth()->user()?->can('view-pipeline'), 403);
    }
}
