<?php

namespace App\Models;

use App\Enums\MessageChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmBroadcast extends Model
{
    protected $fillable = [
        'title',
        'channel',
        'message_template_id',
        'body',
        'filters',
        'recipient_count',
        'sent_by',
        'sent_at',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'filters' => 'array',
        'sent_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class, 'message_template_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
