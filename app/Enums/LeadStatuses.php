<?php

namespace App\Enums;

enum LeadStatuses: string
{
    case Pending = 'pending';
    case Empty = 'empty';
    case Done = 'done';
    case Lost = 'lost';

    public static function labels(): array
    {
        return [
            self::Pending->value => __('Pending'),
            self::Empty->value => __('Empty'),
            self::Done->value => __('Done'),
            self::Lost->value => __('Lost'),
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
            self::Pending => '9333EA', // purple
            self::Empty => 'FBBF24',       // yellow
            self::Done => '22C55E',       // green
            self::Lost => 'EF4444',       // red
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            self::Done, self::Lost, self::Pending => 'FFFFFF', // all white
            self::Empty => '000000', // black
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
