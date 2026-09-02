<?php

namespace App\Enums;

enum LostReason: string
{
    case NoResponse = 'no_response';
    case Price = 'price';
    case Location = 'location';
    case Financing = 'financing';
    case Competitor = 'competitor';
    case NotQualified = 'not_qualified';
    case Other = 'other';

    public static function labels(): array
    {
        return [
            self::NoResponse->value => __('No response'),
            self::Price->value => __('Price'),
            self::Location->value => __('Location'),
            self::Financing->value => __('Financing'),
            self::Competitor->value => __('Chose a competitor'),
            self::NotQualified->value => __('Not qualified'),
            self::Other->value => __('Other'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
