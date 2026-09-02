<?php

namespace App\Enums;

enum CommissionPayoutStatus: string
{
    case Accrued = 'accrued';
    case Approved = 'approved';
    case Paid = 'paid';

    public static function labels(): array
    {
        return [
            self::Accrued->value => __('Accrued'),
            self::Approved->value => __('Approved'),
            self::Paid->value => __('Paid'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::Accrued => 'badge-phoenix-warning',
            self::Approved => 'badge-phoenix-info',
            self::Paid => 'badge-phoenix-success',
        };
    }
}
