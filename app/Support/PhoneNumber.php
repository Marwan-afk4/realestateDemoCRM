<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Normalize a phone number to E.164 (+ then 7–15 digits).
     */
    public static function toE164(string $phone, ?string $defaultCountryCode = null): string
    {
        $defaultCountryCode = $defaultCountryCode ?: config('services.phone.default_country_code', '20');
        $trimmed = trim($phone);
        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($trimmed, '+')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '00')) {
            return '+'.substr($digits, 2);
        }

        if (str_starts_with($digits, $defaultCountryCode)) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+'.$defaultCountryCode.substr($digits, 1);
        }

        return '+'.$defaultCountryCode.$digits;
    }

    public static function isValidE164(string $e164): bool
    {
        return (bool) preg_match('/^\+[1-9]\d{6,14}$/', $e164);
    }

    /**
     * Formats that may already be stored on a user record.
     *
     * @return list<string>
     */
    public static function lookupCandidates(string $phone, ?string $defaultCountryCode = null): array
    {
        $defaultCountryCode = $defaultCountryCode ?: config('services.phone.default_country_code', '20');
        $e164 = self::toE164($phone, $defaultCountryCode);
        $digits = ltrim($e164, '+');

        $national = str_starts_with($digits, $defaultCountryCode)
            ? substr($digits, strlen($defaultCountryCode))
            : $digits;

        return array_values(array_unique(array_filter([
            trim($phone),
            $e164,
            $digits,
            $national !== '' ? '0'.$national : null,
            $national,
        ])));
    }
}
