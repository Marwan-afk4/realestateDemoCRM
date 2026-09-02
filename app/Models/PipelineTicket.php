<?php

namespace App\Models;

use App\Enums\LostReason;
use App\Enums\PipelineStage;
use App\Enums\PipelineTicketType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PipelineTicket extends Model
{
    protected $fillable = [
        'contact_id',
        'owner_id',
        'brocker_id',
        'type',
        'ticketable_type',
        'ticketable_id',
        'stage',
        'lost_reason',
        'lost_note',
        'locked_at',
        'locked_by',
        'probability',
        'stage_changed_at',
        'stage_changed_by',
        'inventory_unit_id',
    ];

    protected $casts = [
        'type' => PipelineTicketType::class,
        'stage' => PipelineStage::class,
        'lost_reason' => LostReason::class,
        'locked_at' => 'datetime',
        'stage_changed_at' => 'datetime',
        'probability' => 'integer',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function brocker(): BelongsTo
    {
        return $this->belongsTo(Brocker::class);
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function stageChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'stage_changed_by');
    }

    public function ticketable(): MorphTo
    {
        return $this->morphTo();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function title(): string
    {
        return $this->contact?->name ?: __('Untitled');
    }

    public function openTask(): ?CrmTask
    {
        return $this->tasks()->whereNull('completed_at')->orderBy('due_at')->first();
    }
}
