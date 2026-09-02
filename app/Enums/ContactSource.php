<?php

namespace App\Enums;

enum ContactSource: string
{
    case Facebook = 'facebook';
    case Website = 'website';
    case Broker = 'broker';
    case Agency = 'agency';
    case WalkIn = 'walk_in';
    case SellRequest = 'sell_request';
    case Mortgage = 'mortgage';
    case Other = 'other';

    public static function labels(): array
    {
        return [
            self::Facebook->value => __('Facebook'),
            self::Website->value => __('Website'),
            self::Broker->value => __('Broker'),
            self::Agency->value => __('Agency'),
            self::WalkIn->value => __('Walk-in'),
            self::SellRequest->value => __('Sell Request'),
            self::Mortgage->value => __('Mortgage Request'),
            self::Other->value => __('Other'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Facebook => 'badge-phoenix badge-phoenix-primary',
            self::Website => 'badge-phoenix badge-phoenix-info',
            self::Broker => 'badge-phoenix badge-phoenix-success',
            self::Agency => 'badge-phoenix badge-phoenix-warning',
            self::WalkIn => 'badge-phoenix badge-phoenix-secondary',
            self::SellRequest => 'badge-phoenix badge-phoenix-warning',
            self::Mortgage => 'badge-phoenix badge-phoenix-info',
            self::Other => 'badge-phoenix badge-phoenix-secondary',
        };
    }
}
