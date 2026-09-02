<?php

namespace App\Enums;

enum HoldType: string
{
    case Eoi = 'eoi';
    case Reservation = 'reservation';

    public static function labels(): array
    {
        return [
            self::Eoi->value => __('EOI hold'),
            self::Reservation->value => __('Reservation'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function defaultHours(): int
    {
        return match ($this) {
            self::Eoi => 48,
            self::Reservation => 24 * 7,
        };
    }

    public function inventoryStatus(): InventoryStatus
    {
        return match ($this) {
            self::Eoi => InventoryStatus::Held,
            self::Reservation => InventoryStatus::Reserved,
        };
    }
}
