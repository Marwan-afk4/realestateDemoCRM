<?php

namespace App\Console\Commands;

use App\Models\CrmTask;
use App\Notifications\CrmTaskReminder;
use Illuminate\Console\Command;

class RemindCrmTasks extends Command
{
    protected $signature = 'crm:remind-tasks';

    protected $description = 'Notify owners of due CRM tasks and assignment expiries';

    public function handle(): int
    {
        $tasks = CrmTask::query()
            ->with(['owner', 'contact'])
            ->whereNull('completed_at')
            ->whereNull('reminder_sent_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->addHours(12))
            ->get();

        $sent = 0;
        foreach ($tasks as $task) {
            if ($task->owner) {
                $task->owner->notify(new CrmTaskReminder($task));
                $sent++;
            }
            $task->forceFill(['reminder_sent_at' => now()])->save();
        }

        $this->info("Sent {$sent} CRM task reminders.");

        return self::SUCCESS;
    }
}
