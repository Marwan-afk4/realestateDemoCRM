<?php

namespace App\Services;

use App\Exceptions\RasilOtpException;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PhoneOtpService
{
    public const PURPOSE_REGISTER = 'register';

    public const PURPOSE_RESET = 'reset';

    public function __construct(
        private readonly RasilOtpService $rasil,
    ) {}

    public function send(string $phone, string $purpose = self::PURPOSE_REGISTER): array
    {
        $purpose = $this->purpose($purpose);
        $e164 = PhoneNumber::toE164($phone);

        if (! PhoneNumber::isValidE164($e164)) {
            throw new RasilOtpException('Invalid phone number', 400, 'invalid_recipient');
        }

        $this->ensureCanSend($e164, $purpose);

        $code = $this->generateCode();
        $ttlMinutes = max(1, (int) config('services.rasil.otp_ttl', 5));
        $expiresAt = now()->addMinutes($ttlMinutes);

        Cache::put($this->cacheKey($e164, $purpose), [
            'hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => $expiresAt->timestamp,
        ], $expiresAt);

        try {
            $result = $this->rasil->send(
                $e164,
                $code,
                $purpose.'-'.$e164.'-'.Str::uuid()->toString(),
            );
        } catch (RasilOtpException $e) {
            Cache::forget($this->cacheKey($e164, $purpose));

            throw $e;
        }

        RateLimiter::hit($this->cooldownKey($e164, $purpose), 60);
        RateLimiter::hit($this->hourlyKey($e164, $purpose), 3600);

        return $result;
    }

    public function verify(string $phone, string $code, string $purpose = self::PURPOSE_REGISTER): bool
    {
        $purpose = $this->purpose($purpose);
        $e164 = PhoneNumber::toE164($phone);
        $key = $this->cacheKey($e164, $purpose);
        $payload = Cache::get($key);

        if (! is_array($payload) || empty($payload['hash'])) {
            return false;
        }

        $attempts = (int) ($payload['attempts'] ?? 0);
        if ($attempts >= 5) {
            Cache::forget($key);

            return false;
        }

        if (! Hash::check($code, $payload['hash'])) {
            $payload['attempts'] = $attempts + 1;
            $ttl = isset($payload['expires_at'])
                ? max(1, (int) $payload['expires_at'] - time())
                : (int) config('services.rasil.otp_ttl', 5) * 60;
            Cache::put($key, $payload, $ttl);

            return false;
        }

        Cache::forget($key);
        RateLimiter::clear($this->cooldownKey($e164, $purpose));
        Cache::put($this->verifiedKey($e164, $purpose), true, now()->addMinutes(30));

        return true;
    }

    public function isVerified(string $phone, string $purpose = self::PURPOSE_REGISTER): bool
    {
        $purpose = $this->purpose($purpose);

        return (bool) Cache::get($this->verifiedKey(PhoneNumber::toE164($phone), $purpose));
    }

    public function consumeVerified(string $phone, string $purpose = self::PURPOSE_REGISTER): bool
    {
        $purpose = $this->purpose($purpose);
        $key = $this->verifiedKey(PhoneNumber::toE164($phone), $purpose);

        if (! Cache::pull($key)) {
            return false;
        }

        return true;
    }

    public function rememberPending(string $phone, array $payload, string $purpose = self::PURPOSE_REGISTER): void
    {
        $purpose = $this->purpose($purpose);

        Cache::put(
            $this->pendingKey(PhoneNumber::toE164($phone), $purpose),
            $payload,
            now()->addMinutes(30),
        );
    }

    public function pullPending(string $phone, string $purpose = self::PURPOSE_REGISTER): ?array
    {
        $purpose = $this->purpose($purpose);
        $payload = Cache::pull($this->pendingKey(PhoneNumber::toE164($phone), $purpose));

        return is_array($payload) ? $payload : null;
    }

    private function ensureCanSend(string $e164, string $purpose): void
    {
        if (RateLimiter::tooManyAttempts($this->cooldownKey($e164, $purpose), 1)) {
            throw new RasilOtpException(
                'Please wait before requesting another code.',
                429,
                'too_many_requests',
            );
        }

        if (RateLimiter::tooManyAttempts($this->hourlyKey($e164, $purpose), 5)) {
            throw new RasilOtpException(
                'Too many verification attempts. Please try again later.',
                429,
                'too_many_requests',
            );
        }
    }

    private function generateCode(): string
    {
        $length = max(4, min(10, (int) config('services.rasil.otp_length', 6)));

        return str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    private function purpose(string $purpose): string
    {
        if (! in_array($purpose, [self::PURPOSE_REGISTER, self::PURPOSE_RESET], true)) {
            throw new InvalidArgumentException('Invalid OTP purpose.');
        }

        return $purpose;
    }

    private function cacheKey(string $e164, string $purpose): string
    {
        return "phone_otp:{$purpose}:{$e164}";
    }

    private function verifiedKey(string $e164, string $purpose): string
    {
        return "phone_otp_verified:{$purpose}:{$e164}";
    }

    private function pendingKey(string $e164, string $purpose): string
    {
        return "phone_otp_pending:{$purpose}:{$e164}";
    }

    private function cooldownKey(string $e164, string $purpose): string
    {
        return "phone_otp_send:{$purpose}:{$e164}";
    }

    private function hourlyKey(string $e164, string $purpose): string
    {
        return "phone_otp_send_hour:{$purpose}:{$e164}";
    }
}
