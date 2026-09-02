<?php

namespace App\Models;

use App\Enums\MessageChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageTemplate extends Model
{
    protected $fillable = [
        'name',
        'channel',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'is_active' => 'boolean',
    ];

    public function broadcasts(): HasMany
    {
        return $this->hasMany(CrmBroadcast::class);
    }

    public function render(array $vars): string
    {
        $body = $this->body;
        foreach ($vars as $key => $value) {
            $body = str_replace('{{'.$key.'}}', (string) $value, $body);
        }

        return $body;
    }
}
