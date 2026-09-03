<?php

namespace App\Enums;

enum AfterSalesTicketType: string
{
    case Snagging = 'snagging';
    case CustomerCare = 'customer_care';
    case Warranty = 'warranty';

    public static function labels(): array
    {
        return [
            self::Snagging->value => __('Snagging'),
            self::CustomerCare->value => __('Customer care'),
            self::Warranty->value => __('Warranty'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Snagging => 'badge-phoenix-warning',
            self::CustomerCare => 'badge-phoenix-info',
            self::Warranty => 'badge-phoenix-secondary',
        };
    }
}
