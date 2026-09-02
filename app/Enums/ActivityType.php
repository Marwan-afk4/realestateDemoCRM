<?php

namespace App\Enums;

enum ActivityType: string
{
    case Note = 'note';
    case Call = 'call';
    case Whatsapp = 'whatsapp';
    case Sms = 'sms';
    case Email = 'email';
    case Meeting = 'meeting';
    case SiteVisit = 'site_visit';
    case StageChange = 'stage_change';
    case Assignment = 'assignment';
    case Transfer = 'transfer';
    case Document = 'document';
    case Task = 'task';
    case System = 'system';

    public static function labels(): array
    {
        return [
            self::Note->value => __('Note'),
            self::Call->value => __('Call'),
            self::Whatsapp->value => __('WhatsApp'),
            self::Sms->value => __('SMS'),
            self::Email->value => __('Email'),
            self::Meeting->value => __('Meeting'),
            self::SiteVisit->value => __('Site Visit'),
            self::StageChange->value => __('Stage Change'),
            self::Assignment->value => __('Assignment'),
            self::Transfer->value => __('Transfer'),
            self::Document->value => __('Document'),
            self::Task->value => __('Task'),
            self::System->value => __('System'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function icon(): string
    {
        return match ($this) {
            self::Note => 'edit-3',
            self::Call => 'phone',
            self::Whatsapp => 'message-circle',
            self::Sms => 'smartphone',
            self::Email => 'mail',
            self::Meeting => 'users',
            self::SiteVisit => 'map-pin',
            self::StageChange => 'git-commit',
            self::Assignment => 'user-plus',
            self::Transfer => 'shuffle',
            self::Document => 'file-text',
            self::Task => 'check-square',
            self::System => 'info',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Note => '6c757d',
            self::Call => '3874ff',
            self::Whatsapp => '25b003',
            self::Sms => '0097c2',
            self::Email => 'e5780b',
            self::Meeting => '6f42c1',
            self::SiteVisit => 'f5803e',
            self::StageChange => '3874ff',
            self::Assignment => '0097c2',
            self::Transfer => 'e5780b',
            self::Document => '6c757d',
            self::Task => '25b003',
            self::System => '8a94ad',
        };
    }
}
