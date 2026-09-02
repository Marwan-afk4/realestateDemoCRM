<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    protected $fillable = [
        'contact_id',
        'pipeline_ticket_id',
        'user_id',
        'type',
        'title',
        'body',
        'meta',
    ];

    protected $casts = [
        'type' => ActivityType::class,
        'meta' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(PipelineTicket::class, 'pipeline_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
