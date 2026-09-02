<?php

namespace App\Enums;

enum SaleOfferStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Expired = 'expired';
    case Superseded = 'superseded';

    public static function labels(): array
    {
        return [
            self::Draft->value => __('Draft'),
            self::Sent->value => __('Sent'),
            self::Accepted->value => __('Accepted'),
            self::Expired->value => __('Expired'),
            self::Superseded->value => __('Superseded'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::Draft => 'badge-phoenix-secondary',
            self::Sent => 'badge-phoenix-info',
            self::Accepted => 'badge-phoenix-success',
            self::Expired, self::Superseded => 'badge-phoenix-secondary',
        };
    }
}
