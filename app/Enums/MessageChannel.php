<?php

namespace App\Enums;

enum MessageChannel: string
{
    case Whatsapp = 'whatsapp';
    case Sms = 'sms';
    case Email = 'email';

    public static function labels(): array
    {
        return [
            self::Whatsapp->value => __('WhatsApp'),
            self::Sms->value => __('SMS'),
            self::Email->value => __('Email'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Whatsapp => 'badge-phoenix badge-phoenix-success',
            self::Sms => 'badge-phoenix badge-phoenix-info',
            self::Email => 'badge-phoenix badge-phoenix-warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Whatsapp => 'whatsapp',
            self::Sms => 'comment-sms',
            self::Email => 'envelope',
        };
    }
}
