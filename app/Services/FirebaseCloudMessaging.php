<?php

namespace App\Services;

use App\Models\DeviceToken;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FirebaseCloudMessaging
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($tokens as $token) {
            $result = $this->sendToToken($token, $title, $body, $data);

            if ($result['ok']) {
                $sent++;
                continue;
            }

            $failed++;
            $errors[] = $result['error'] ?? 'Unknown error';

            if ($result['invalid'] ?? false) {
                DeviceToken::where('token', $token)->delete();
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 10),
        ];
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        $projectId = config('services.firebase.project_id');
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $this->stringifyData(array_merge([
                    'type' => 'admin_push',
                    'title' => $title,
                    'body' => $body,
                ], $data)),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($this->accessToken())
                ->acceptJson()
                ->timeout(20)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);
        } catch (\Throwable $e) {
            Log::warning('FCM request failed', ['error' => $e->getMessage()]);

            return ['ok' => false, 'invalid' => false, 'error' => $e->getMessage()];
        }

        if ($response->successful()) {
            return ['ok' => true, 'invalid' => false];
        }

        $status = data_get($response->json(), 'error.status');
        $message = data_get($response->json(), 'error.message', $response->body());
        $messageText = is_string($message) ? $message : 'FCM send failed';
        $invalid = in_array($status, ['UNREGISTERED', 'NOT_FOUND'], true)
            || ($status === 'INVALID_ARGUMENT' && str_contains(strtolower($messageText), 'token'));

        Log::info('FCM send failed', [
            'status' => $status,
            'message' => $message,
        ]);

        return [
            'ok' => false,
            'invalid' => $invalid,
            'error' => $messageText,
        ];
    }

    private function accessToken(): string
    {
        return Cache::remember('firebase_fcm_access_token', now()->addMinutes(50), function () {
            $credentials = $this->credentials();
            $now = time();

            $jwt = JWT::encode([
                'iss' => $credentials['client_email'],
                'sub' => $credentials['client_email'],
                'aud' => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => self::SCOPE,
            ], $credentials['private_key'], 'RS256');

            $response = Http::asForm()
                ->timeout(15)
                ->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

            if (! $response->successful() || ! $response->json('access_token')) {
                throw new RuntimeException(
                    'Unable to obtain Firebase access token: '.$response->body()
                );
            }

            return $response->json('access_token');
        });
    }

    private function credentials(): array
    {
        $path = config('services.firebase.credentials');

        if (! $path) {
            throw new RuntimeException('Firebase credentials path is not configured.');
        }

        $resolved = str_starts_with($path, '/') ? $path : base_path($path);

        if (! is_file($resolved)) {
            throw new RuntimeException("Firebase credentials file not found at {$resolved}.");
        }

        $credentials = json_decode((string) file_get_contents($resolved), true);

        if (! is_array($credentials) || empty($credentials['private_key']) || empty($credentials['client_email'])) {
            throw new RuntimeException('Firebase credentials file is invalid.');
        }

        return $credentials;
    }

    private function stringifyData(array $data): array
    {
        $stringData = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        return $stringData;
    }
}
