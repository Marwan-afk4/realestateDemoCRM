<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case Available = 'available';
    case Held = 'held';
    case Reserved = 'reserved';
    case Contracted = 'contracted';
    case Sold = 'sold';
    case HandedOver = 'handed_over';

    public static function labels(): array
    {
        return [
            self::Available->value => __('Available'),
            self::Held->value => __('Held'),
            self::Reserved->value => __('Reserved'),
            self::Contracted->value => __('Contracted'),
            self::Sold->value => __('Sold'),
            self::HandedOver->value => __('Handed over'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::Available => 'badge-phoenix-success',
            self::Held => 'badge-phoenix-info',
            self::Reserved => 'badge-phoenix-warning',
            self::Contracted => 'badge-phoenix-primary',
            self::Sold => 'badge-phoenix-secondary',
            self::HandedOver => 'badge-phoenix-secondary',
        };
    }

    public function blocksOtherSale(): bool
    {
        return $this !== self::Available;
    }

    public function isOpenStock(): bool
    {
        return $this === self::Available || $this === self::Held;
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->allowedNext(), true);
    }

    public function allowedNext(): array
    {
        return match ($this) {
            self::Available => [self::Held, self::Reserved, self::Contracted, self::Sold],
            self::Held => [self::Available, self::Reserved, self::Contracted, self::Sold],
            self::Reserved => [self::Available, self::Contracted, self::Sold],
            self::Contracted => [self::Sold, self::Available],
            self::Sold => [self::HandedOver],
            self::HandedOver => [],
        };
    }

    public static function fromUptownStatus(?string $status): self
    {
        return match ($status) {
            'reserved' => self::Reserved,
            'sold' => self::Sold,
            default => self::Available,
        };
    }

    public function toUptownStatus(): string
    {
        return match ($this) {
            self::Sold, self::HandedOver => 'sold',
            self::Held, self::Reserved, self::Contracted => 'reserved',
            self::Available => 'available',
        };
    }
}
