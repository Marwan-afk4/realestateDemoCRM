<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Enums\MessageChannel;
use App\Models\Contact;
use App\Models\User;

class MessagingService
{
    public function __construct(private ActivityLogger $activities)
    {
    }

    public function channelActivityType(MessageChannel $channel): ActivityType
    {
        return match ($channel) {
            MessageChannel::Whatsapp => ActivityType::Whatsapp,
            MessageChannel::Sms => ActivityType::Sms,
            MessageChannel::Email => ActivityType::Email,
        };
    }

    public function deepLink(Contact $contact, MessageChannel $channel, string $body, ?string $subject = null): ?string
    {
        return match ($channel) {
            MessageChannel::Whatsapp => $contact->whatsappLink()
                ? $contact->whatsappLink().'?text='.rawurlencode($body)
                : null,
            MessageChannel::Email => $contact->email
                ? 'mailto:'.$contact->email.'?subject='.rawurlencode($subject ?? '').'&body='.rawurlencode($body)
                : null,
            MessageChannel::Sms => $contact->phone_e164 || $contact->phone
                ? 'sms:'.ltrim((string) ($contact->phone_e164 ?: $contact->phone), '+').'?body='.rawurlencode($body)
                : null,
        };
    }

    public function prepareOutbound(
        Contact $contact,
        MessageChannel $channel,
        string $body,
        ?User $user = null,
        ?string $subject = null,
        ?string $title = null,
        array $meta = [],
    ): array {
        $this->activities->log(
            $contact,
            $this->channelActivityType($channel),
            $title ?? $channel->label(),
            $body,
            array_merge(['subject' => $subject, 'outbound' => true], $meta),
            null,
            $user,
        );

        $contact->forceFill(['last_contacted_at' => now()])->saveQuietly();

        return [
            'link' => $this->deepLink($contact, $channel, $body, $subject),
            'channel' => $channel,
        ];
    }

    public function logInbound(
        Contact $contact,
        MessageChannel $channel,
        string $body,
        ?User $user = null,
        ?string $subject = null,
    ): void {
        $this->activities->log(
            $contact,
            $this->channelActivityType($channel),
            __('Inbound :channel', ['channel' => $channel->label()]),
            $body,
            ['subject' => $subject, 'inbound' => true],
            null,
            $user,
        );

        $contact->forceFill(['last_contacted_at' => now()])->saveQuietly();
    }
}
