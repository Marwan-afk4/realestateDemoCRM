<?php

namespace App\Notifications;

use App\Models\CrmTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CrmTaskReminder extends Notification
{
    use Queueable;

    public function __construct(public CrmTask $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_at' => optional($this->task->due_at)?->toIso8601String(),
            'contact_id' => $this->task->contact_id,
            'contact_name' => $this->task->contact?->name,
            'type' => $this->task->type?->value,
        ];
    }
}
