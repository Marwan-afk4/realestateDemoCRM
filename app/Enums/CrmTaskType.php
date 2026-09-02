<?php

namespace App\Enums;

enum CrmTaskType: string
{
    case Call = 'call';
    case Visit = 'visit';
    case SendOffer = 'send_offer';
    case AssignmentExpiry = 'assignment_expiry';
    case Other = 'other';

    public static function labels(): array
    {
        return [
            self::Call->value => __('Call back'),
            self::Visit->value => __('Compound visit'),
            self::SendOffer->value => __('Send offer PDF'),
            self::AssignmentExpiry->value => __('Assignment expiry'),
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
            self::Call => 'badge-phoenix badge-phoenix-primary',
            self::Visit => 'badge-phoenix badge-phoenix-info',
            self::SendOffer => 'badge-phoenix badge-phoenix-warning',
            self::AssignmentExpiry => 'badge-phoenix badge-phoenix-danger',
            self::Other => 'badge-phoenix badge-phoenix-secondary',
        };
    }
}
