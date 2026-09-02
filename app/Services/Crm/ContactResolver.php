<?php

namespace App\Services\Crm;

use App\Enums\ContactSource;
use App\Models\Contact;
use App\Support\PhoneNumber;

class ContactResolver
{
    public function findOrCreate(array $attributes): Contact
    {
        $phone = $attributes['phone'] ?? null;
        $contact = $phone ? Contact::findByPhone($phone) : null;

        if (! $contact && ! empty($attributes['email'])) {
            $contact = Contact::query()->where('email', $attributes['email'])->first();
        }

        $payload = $this->normalize($attributes);

        if ($contact) {
            foreach ($payload as $key => $value) {
                if ($value === null || $value === '' || $value === []) {
                    continue;
                }
                if ($key === 'tags') {
                    $contact->tags = array_values(array_unique(array_merge($contact->tagsList(), (array) $value)));
                    continue;
                }
                if ($contact->{$key} === null || $contact->{$key} === '') {
                    $contact->{$key} = $value;
                }
            }
            $contact->save();

            return $contact;
        }

        return Contact::create($payload);
    }

    private function normalize(array $attributes): array
    {
        $phone = $attributes['phone'] ?? null;
        $e164 = $phone ? PhoneNumber::toE164($phone) : null;

        $source = $attributes['source'] ?? ContactSource::Other;
        if (is_string($source)) {
            $source = ContactSource::tryFrom($source) ?? ContactSource::Other;
        }

        return [
            'name' => $attributes['name'] ?? 'Unknown',
            'phone' => $phone,
            'phone_e164' => $e164 && PhoneNumber::isValidE164($e164) ? $e164 : null,
            'phone_secondary' => $attributes['phone_secondary'] ?? null,
            'email' => $attributes['email'] ?? null,
            'whatsapp' => $attributes['whatsapp'] ?? ($e164 ?: $phone),
            'national_id' => $attributes['national_id'] ?? null,
            'source' => $source,
            'budget_min' => $attributes['budget_min'] ?? null,
            'budget_max' => $attributes['budget_max'] ?? null,
            'preferred_area' => $attributes['preferred_area'] ?? null,
            'uptown_type_id' => $attributes['uptown_type_id'] ?? null,
            'intent' => $attributes['intent'] ?? null,
            'payment_preference' => $attributes['payment_preference'] ?? null,
            'tags' => $attributes['tags'] ?? [],
            'owner_id' => $attributes['owner_id'] ?? null,
            'notes' => $attributes['notes'] ?? null,
        ];
    }
}
