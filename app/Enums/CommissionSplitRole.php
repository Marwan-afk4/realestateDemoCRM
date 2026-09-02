<?php

namespace App\Enums;

enum CommissionSplitRole: string
{
    case Lister = 'lister';
    case Closer = 'closer';
    case Manager = 'manager';

    public static function labels(): array
    {
        return [
            self::Lister->value => __('Lister'),
            self::Closer->value => __('Closer'),
            self::Manager->value => __('Manager'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
