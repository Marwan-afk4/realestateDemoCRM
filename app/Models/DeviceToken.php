<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'platform',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function register(User $user, string $token, ?string $platform = null): self
    {
        return static::query()->updateOrCreate(
            ['token' => $token],
            [
                'user_id' => $user->id,
                'platform' => $platform,
            ]
        );
    }

    public static function syncFromRequest(User $user, Request $request): void
    {
        $token = $request->input('fcm_token') ?? $request->input('device_token');

        if (! is_string($token) || trim($token) === '') {
            return;
        }

        $platform = $request->input('platform');

        static::register(
            $user,
            trim($token),
            is_string($platform) && $platform !== '' ? strtolower($platform) : null
        );
    }
}
