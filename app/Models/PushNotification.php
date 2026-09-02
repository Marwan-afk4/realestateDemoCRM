<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'audience',
        'user_ids',
        'sent_count',
        'failed_count',
        'sent_by',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
