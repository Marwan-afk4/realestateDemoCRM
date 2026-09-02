<?php

namespace App\Enums;

enum DealStatuses: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case SemiDone = 'semidone';

    public static function labels(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Approved->value => 'Approved',
            self::Rejected->value => 'Rejected',
            self::SemiDone->value => 'Semi Done',
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'FFA500', // orange
            self::Approved => '28A745', // green
            self::Rejected => 'DC3545', // red
            self::SemiDone => '6F42C1' //purple
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            self::Pending => '000000', // black
            self::Approved => 'FFFFFF', // white
            self::Rejected => 'FFFFFF', // white
            self::SemiDone => 'FFFFFF', // white
        };
    }

    public function badge(): string
    {
        return sprintf(
            '<span class="badge rounded-pill px-3 py-2" style="background-color: #%s; color: #%s;">%s</span>',
            $this->color(),
            $this->textColor(),
            $this->label()
        );
    }
}
