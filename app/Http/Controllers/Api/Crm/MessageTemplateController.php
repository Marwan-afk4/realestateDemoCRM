<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\MessageChannel;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageTemplateController extends Controller
{
    use RespondsJson;

    public function index()
    {
        $this->authorizeView();
        $templates = MessageTemplate::query()->latest()->paginate(30);

        return $this->paginated($templates);
    }

    public function store(Request $request)
    {
        $this->authorizeView();
        $template = MessageTemplate::create($this->validated($request));

        return $this->created($template, __('Created successfully'));
    }

    public function show(MessageTemplate $message_template)
    {
        $this->authorizeView();

        return $this->ok($message_template);
    }

    public function update(Request $request, MessageTemplate $message_template)
    {
        $this->authorizeView();
        $message_template->update($this->validated($request));

        return $this->ok($message_template->fresh(), __('Updated successfully.'));
    }

    public function destroy(MessageTemplate $message_template)
    {
        $this->authorizeView();
        $message_template->delete();

        return $this->ok(null, __('Deleted successfully'));
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
