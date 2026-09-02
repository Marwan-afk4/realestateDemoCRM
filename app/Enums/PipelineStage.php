<?php

namespace App\Enums;

enum PipelineStage: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Viewing = 'viewing';
    case Negotiation = 'negotiation';
    case Reserved = 'reserved';
    case Won = 'won';
    case Lost = 'lost';

    public static function labels(): array
    {
        return [
            self::New->value => __('New'),
            self::Contacted->value => __('Contacted'),
            self::Qualified->value => __('Qualified'),
            self::Viewing->value => __('Viewing'),
            self::Negotiation->value => __('Negotiation'),
            self::Reserved->value => __('Reserved'),
            self::Won->value => __('Won'),
            self::Lost->value => __('Lost'),
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function open(): array
    {
        return [
            self::New,
            self::Contacted,
            self::Qualified,
            self::Viewing,
            self::Negotiation,
            self::Reserved,
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function isOpen(): bool
    {
        return in_array($this, self::open(), true);
    }

    public function isTerminal(): bool
    {
        return $this === self::Won || $this === self::Lost;
    }

    public function color(): string
    {
        return match ($this) {
            self::New => '6B7280',
            self::Contacted => '3B82F6',
            self::Qualified => '0EA5E9',
            self::Viewing => '8B5CF6',
            self::Negotiation => 'F59E0B',
            self::Reserved => 'F97316',
            self::Won => '22C55E',
            self::Lost => 'EF4444',
        };
    }

    public function textColor(): string
    {
        return 'FFFFFF';
    }

    public function phoenixBadge(): string
    {
        return match ($this) {
            self::New => 'badge-phoenix-secondary',
            self::Contacted => 'badge-phoenix-primary',
            self::Qualified => 'badge-phoenix-info',
            self::Viewing => 'badge-phoenix-info',
            self::Negotiation => 'badge-phoenix-warning',
            self::Reserved => 'badge-phoenix-warning',
            self::Won => 'badge-phoenix-success',
            self::Lost => 'badge-phoenix-danger',
        };
    }

    public function phoenixBorder(): string
    {
        return match ($this) {
            self::New => 'border-secondary',
            self::Contacted => 'border-primary',
            self::Qualified => 'border-info',
            self::Viewing => 'border-primary',
            self::Negotiation => 'border-warning',
            self::Reserved => 'border-warning',
            self::Won => 'border-success',
            self::Lost => 'border-danger',
        };
    }

    public function probability(): int
    {
        return match ($this) {
            self::New => 10,
            self::Contacted => 20,
            self::Qualified => 35,
            self::Viewing => 50,
            self::Negotiation => 70,
            self::Reserved => 90,
            self::Won => 100,
            self::Lost => 0,
        };
    }

    public function badge(): string
    {
        return sprintf(
            '<span class="badge badge-phoenix %s">%s</span>',
            $this->phoenixBadge(),
            e($this->label())
        );
    }

    public static function fromLegacyLeadStatus(?string $status): self
    {
        return match ($status) {
            'done' => self::Won,
            'lost' => self::Lost,
            'pending', 'empty', 'in_progress' => self::New,
            default => self::New,
        };
    }

    public function toLegacyLeadStatus(): string
    {
        return match ($this) {
            self::Won => 'done',
            self::Lost => 'lost',
            default => 'pending',
        };
    }
}
