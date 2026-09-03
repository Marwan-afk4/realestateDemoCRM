<?php

namespace App\Enums;

enum AfterSalesTicketStatus: string
{
    case Open = 'open';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public static function labels(): array
    {
        return [
            self::Open->value => __('Open'),
            self::Scheduled->value => __('Scheduled'),
            self::InProgress->value => __('In progress'),
            self::Resolved->value => __('Resolved'),
            self::Closed->value => __('Closed'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::Open => 'badge-phoenix-danger',
            self::Scheduled => 'badge-phoenix-warning',
            self::InProgress => 'badge-phoenix-info',
            self::Resolved => 'badge-phoenix-success',
            self::Closed => 'badge-phoenix-secondary',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Resolved, self::Closed], true);
    }
}
