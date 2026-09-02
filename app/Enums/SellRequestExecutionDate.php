<?php

namespace App\Enums;

enum SellRequestExecutionDate: string
{
    case Immediately = 'immediately';
    case SixMonths = '6_months';
    case OneYear = '1_year';
    case TwoYears = '2_years';
    case ThreeYears = '3_years';
    case FourYearsOrMore = '4_years_or_more';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::Immediately->value => __('Immediately'),
            self::SixMonths->value => __('6 Months'),
            self::OneYear->value => __('1 Year'),
            self::TwoYears->value => __('2 Years'),
            self::ThreeYears->value => __('3 Years'),
            self::FourYearsOrMore->value => __('4 Years or More'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
