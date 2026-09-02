<?php

use App\Exceptions\RasilOtpException;
use App\Services\PhoneOtpService;
use App\Services\RasilOtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    Cache::flush();
    RateLimiter::clear('phone_otp_send:register:+201000000000');
    RateLimiter::clear('phone_otp_send_hour:register:+201000000000');
    RateLimiter::clear('phone_otp_send:reset:+201000000000');
    RateLimiter::clear('phone_otp_send_hour:reset:+201000000000');
    config([
        'services.rasil.token' => 'test-rasil-token',
        'services.rasil.base_url' => 'https://api.rasil.io/developer/api/v2',
        'services.rasil.otp_ttl' => 5,
        'services.rasil.otp_length' => 6,
        'services.phone.default_country_code' => '20',
    ]);
});

it('sends a 4-10 digit code to rasil and stores a hash locally', function () {
    Http::fake([
        'https://api.rasil.io/developer/api/v2/otp/send' => Http::response([
            'id' => 'msg_1',
            'status' => 'sent',
            'deduplicated' => false,
        ], 200),
    ]);

    $result = app(PhoneOtpService::class)->send('01000000000');

    expect($result['status'])->toBe('sent')
        ->and(Cache::get('phone_otp:register:+201000000000'))->toHaveKey('hash');

    Http::assertSent(function ($request) {
        $code = $request['code'] ?? null;

        return $request->url() === 'https://api.rasil.io/developer/api/v2/otp/send'
            && $request['to'] === '+201000000000'
            && is_string($code)
            && preg_match('/^[A-Za-z0-9]{4,10}$/', $code)
            && is_string($request['idempotency_key'])
            && str_starts_with($request['idempotency_key'], 'register-')
            && $request->hasHeader('Authorization', 'Bearer test-rasil-token');
    });
});

it('verifies the stored code and marks the phone for registration', function () {
    Cache::put('phone_otp:register:+201000000000', [
        'hash' => Hash::make('483920'),
        'attempts' => 0,
        'expires_at' => now()->addMinutes(5)->timestamp,
    ], now()->addMinutes(5));

    $otp = app(PhoneOtpService::class);

    expect($otp->verify('01000000000', '000000'))->toBeFalse()
        ->and($otp->verify('+201000000000', '483920'))->toBeTrue()
        ->and($otp->verify('+201000000000', '483920'))->toBeFalse()
        ->and($otp->isVerified('01000000000'))->toBeTrue()
        ->and($otp->consumeVerified('01000000000'))->toBeTrue()
        ->and($otp->consumeVerified('01000000000'))->toBeFalse();
});

it('stores and returns pending registration data for the same phone', function () {
    $otp = app(PhoneOtpService::class);
    $otp->rememberPending('01283337172', [
        'email' => 'ahmed@example.com',
        'phone' => '01283337172',
    ]);

    expect($otp->pullPending('+201283337172'))->toMatchArray([
        'email' => 'ahmed@example.com',
        'phone' => '01283337172',
    ])->and($otp->pullPending('01283337172'))->toBeNull();
});

it('maps rasil sender_unusable to a configuration error', function () {
    Http::fake([
        'https://api.rasil.io/developer/api/v2/otp/send' => Http::response([
            'error' => 'sender_unusable',
        ], 409),
    ]);

    $client = app(RasilOtpService::class);

    try {
        $client->send('+201283337172', '483920', 'signup-42');
        expect(false)->toBeTrue();
    } catch (RasilOtpException $e) {
        expect($e->status)->toBe(409)
            ->and($e->errorCode)->toBe('sender_unusable')
            ->and($e->getMessage())->toContain('usable WhatsApp number');
    }
});

it('maps rasil no_credit to a payment error', function () {
    Http::fake([
        'https://api.rasil.io/developer/api/v2/otp/send' => Http::response([
            'error' => 'no_credit',
        ], 402),
    ]);

    $client = app(RasilOtpService::class);

    try {
        $client->send('+201000000000', '483920', 'signup-42');
        expect(false)->toBeTrue();
    } catch (RasilOtpException $e) {
        expect($e->status)->toBe(402)
            ->and($e->errorCode)->toBe('no_credit');
    }
});

it('rejects an invalid phone before calling rasil', function () {
    $this->postJson('/api/register/phone', ['phone' => '12'])
        ->assertStatus(400)
        ->assertJson(['error' => 'Invalid phone number']);
});

it('requires a phone number to send an otp', function () {
    $this->postJson('/api/register/phone', [])
        ->assertStatus(401);
});

it('does not log the user in when verifying a registration otp', function () {
    Cache::put('phone_otp:register:+201000000000', [
        'hash' => Hash::make('483920'),
        'attempts' => 0,
        'expires_at' => now()->addMinutes(5)->timestamp,
    ], now()->addMinutes(5));

    $this->postJson('/api/register/phone/verify', [
        'phone' => '01000000000',
        'code' => '483920',
    ])
        ->assertOk()
        ->assertJson(['message' => 'Phone number verified'])
        ->assertJsonMissing(['token']);
});

it('sends a reset otp separately from registration', function () {
    Http::fake([
        'https://api.rasil.io/developer/api/v2/otp/send' => Http::response([
            'id' => 'msg_2',
            'status' => 'sent',
            'deduplicated' => false,
        ], 200),
    ]);

    $result = app(PhoneOtpService::class)->send('01000000000', PhoneOtpService::PURPOSE_RESET);

    expect($result['status'])->toBe('sent')
        ->and(Cache::get('phone_otp:reset:+201000000000'))->toHaveKey('hash')
        ->and(Cache::get('phone_otp:register:+201000000000'))->toBeNull();

    Http::assertSent(fn ($request) => str_starts_with($request['idempotency_key'], 'reset-'));
});

it('requires a phone and new password to start forgot password', function () {
    $this->postJson('/api/forgot-password', ['phone' => '01283337172'])
        ->assertStatus(401);
});

it('rejects an invalid phone on forgot password', function () {
    $this->postJson('/api/forgot-password', [
        'phone' => '12',
        'password' => 'secret1',
    ])
        ->assertStatus(400)
        ->assertJson(['error' => 'Invalid phone number']);
});
