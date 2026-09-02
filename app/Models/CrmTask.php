<?php

namespace App\Models;

use App\Enums\CrmTaskType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTask extends Model
{
    protected $fillable = [
        'contact_id',
        'pipeline_ticket_id',
        'owner_id',
        'created_by',
        'type',
        'title',
        'body',
        'due_at',
        'completed_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'type' => CrmTaskType::class,
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(PipelineTicket::class, 'pipeline_ticket_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted() && $this->due_at && $this->due_at->isPast();
    }
}
