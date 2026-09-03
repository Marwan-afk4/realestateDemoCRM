<?php

namespace App\Models;

use App\Enums\AfterSalesTicketStatus;
use App\Enums\AfterSalesTicketType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AfterSalesTicket extends Model
{
    protected $fillable = [
        'deal_id',
        'inventory_unit_id',
        'contact_id',
        'developer_id',
        'compound_id',
        'type',
        'status',
        'priority',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'scheduled_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'type' => AfterSalesTicketType::class,
        'status' => AfterSalesTicketStatus::class,
        'scheduled_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function compound(): BelongsTo
    {
        return $this->belongsTo(Compound::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
