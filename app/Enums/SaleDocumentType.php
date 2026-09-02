<?php

namespace App\Enums;

enum SaleDocumentType: string
{
    case Offer = 'offer';
    case ReservationLetter = 'reservation_letter';
    case SaleContract = 'sale_contract';

    public static function labels(): array
    {
        return [
            self::Offer->value => __('Offer'),
            self::ReservationLetter->value => __('Reservation letter'),
            self::SaleContract->value => __('Sale contract'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
