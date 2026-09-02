<?php

namespace App\Enums;

use App\Models\BuyAppartmentInstallment;
use App\Models\Lead;
use App\Models\SellRequest;
use Illuminate\Database\Eloquent\Model;

enum PipelineTicketType: string
{
    case Lead = 'lead';
    case SellRequest = 'sell_request';
    case Mortgage = 'mortgage';

    public static function labels(): array
    {
        return [
            self::Lead->value => __('Lead'),
            self::SellRequest->value => __('Sell Request'),
            self::Mortgage->value => __('Mortgage Request'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Lead => 'badge-phoenix badge-phoenix-primary',
            self::SellRequest => 'badge-phoenix badge-phoenix-warning',
            self::Mortgage => 'badge-phoenix badge-phoenix-info',
        };
    }

    public static function fromModel(Model $model): self
    {
        return match (true) {
            $model instanceof Lead => self::Lead,
            $model instanceof SellRequest => self::SellRequest,
            $model instanceof BuyAppartmentInstallment => self::Mortgage,
            default => throw new \InvalidArgumentException('Unsupported pipeline ticketable: '.$model::class),
        };
    }
}
