<?php

use App\Support\PhoneNumber;

it('normalizes local egyptian numbers to e164', function () {
    expect(PhoneNumber::toE164('01000000000', '20'))->toBe('+201000000000')
        ->and(PhoneNumber::toE164('1000000000', '20'))->toBe('+201000000000')
        ->and(PhoneNumber::toE164('201000000000', '20'))->toBe('+201000000000')
        ->and(PhoneNumber::toE164('+201000000000', '20'))->toBe('+201000000000')
        ->and(PhoneNumber::toE164('00201000000000', '20'))->toBe('+201000000000');
});

it('accepts already-valid e164 numbers from other countries', function () {
    expect(PhoneNumber::toE164('+966500000000', '20'))->toBe('+966500000000')
        ->and(PhoneNumber::isValidE164('+966500000000'))->toBeTrue();
});

it('rejects values that are not e164', function () {
    expect(PhoneNumber::isValidE164('01000000000'))->toBeFalse()
        ->and(PhoneNumber::isValidE164('+12'))->toBeFalse();
});

it('builds lookup candidates for stored local and e164 phones', function () {
    $candidates = PhoneNumber::lookupCandidates('01000000000', '20');

    expect($candidates)->toContain('01000000000')
        ->toContain('+201000000000')
        ->toContain('201000000000')
        ->toContain('1000000000');
});
