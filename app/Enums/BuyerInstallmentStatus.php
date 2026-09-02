<?php

namespace App\Enums;

enum BuyerInstallmentStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';

    public static function labels(): array
    {
        return [
            self::Pending->value => __('Pending'),
            self::Partial->value => __('Partial'),
            self::Paid->value => __('Paid'),
            self::Overdue->value => __('Overdue'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::Pending => 'badge-phoenix-secondary',
            self::Partial => 'badge-phoenix-warning',
            self::Paid => 'badge-phoenix-success',
            self::Overdue => 'badge-phoenix-danger',
        };
    }
}
