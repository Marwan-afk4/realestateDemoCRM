<?php

namespace App\Services;

use App\Exceptions\RasilOtpException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RasilOtpService
{
    /**
     * Deliver a one-time code over WhatsApp. Rasil does not store or verify it.
     *
     * @see https://docs.rasil.io/#tag/otp/POST/otp/send
     */
    public function send(string $to, string $code, string $idempotencyKey): array
    {
        $token = config('services.rasil.token');

        if (! $token) {
            throw new RasilOtpException('WhatsApp OTP is not configured', 500, 'not_configured');
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout((int) config('services.rasil.timeout', 15))
                ->post(rtrim((string) config('services.rasil.base_url'), '/').'/otp/send', [
                    'to' => $to,
                    'code' => $code,
                    'idempotency_key' => $idempotencyKey,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Rasil OTP request failed to connect', [
                'to' => $to,
                'message' => $e->getMessage(),
            ]);

            throw new RasilOtpException('Unable to send verification code. Please try again.', 502, 'connection_failed');
        }

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $errorCode = $this->errorCode($response->json());

        Log::warning('Rasil OTP send failed', [
            'to' => $to,
            'status' => $response->status(),
            'error' => $errorCode,
            'body' => $response->json(),
        ]);

        throw new RasilOtpException(
            $this->userMessage($response->status(), $errorCode),
            $this->clientStatus($response->status()),
            $errorCode,
        );
    }

    private function errorCode(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        foreach (['error', 'code', 'type'] as $key) {
            if (isset($payload[$key]) && is_string($payload[$key])) {
                return $payload[$key];
            }
        }

        return null;
    }

    private function userMessage(int $status, ?string $errorCode): string
    {
        return match ($errorCode) {
            'invalid_recipient' => 'Invalid phone number',
            'invalid_code' => 'Invalid verification code format',
            'no_credit' => 'Rasil OTP credits are exhausted. Buy more credits to send codes.',
            'disabled' => 'Rasil OTP is disabled for this account. Ask Rasil to enable it.',
            'no_sender', 'sender_unusable' => 'Rasil has not assigned a usable WhatsApp number to this account. Ask Rasil to configure a sender.',
            default => match ($status) {
                400 => 'Invalid phone number',
                403 => 'The Rasil API key is missing the otp:send scope.',
                409 => 'Rasil OTP is not configured for this account. Ask Rasil to enable it.',
                429 => 'Too many verification attempts. Please try again later.',
                default => 'Unable to send verification code. Please try again.',
            },
        };
    }

    private function clientStatus(int $status): int
    {
        return match ($status) {
            400, 402, 403, 409, 429 => $status,
            502 => 502,
            default => 502,
        };
    }
}
