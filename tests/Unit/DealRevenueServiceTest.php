<?php

use App\Enums\DealStatuses;
use App\Models\Commission;
use App\Models\Compound;
use App\Models\Deal;
use App\Models\InventoryUnit;
use App\Models\Brocker;
use App\Services\Crm\DealRevenueService;
use Tests\TestCase;

uses(TestCase::class);

it('uses commission snapshot amount when present', function () {
    $deal = new Deal;
    $deal->forceFill(['status' => DealStatuses::Approved]);
    $deal->setRelation('commission', new Commission(['amount' => 75_000]));

    expect(app(DealRevenueService::class)->revenueForDeal($deal))->toBe(75000.0);
});

it('estimates from physical unit price and broker commission when no snapshot', function () {
    $unit = new InventoryUnit;
    $unit->forceFill(['current_price' => 2_000_000]);

    $broker = new Brocker;
    $broker->forceFill(['comission_percentage' => 3]);

    $deal = new Deal;
    $deal->forceFill([
        'status' => DealStatuses::Approved,
        'value' => null,
    ]);
    $deal->setRelation('commission', null);
    $deal->setRelation('inventoryUnit', $unit);
    $deal->setRelation('brocker', $broker);
    $deal->setRelation('compound', null);
    $deal->setRelation('activeOffer', null);

    expect(app(DealRevenueService::class)->estimateForDeal($deal))->toBe(60000.0);
});

it('maps legacy unsold listing status to available inventory status', function () {
    expect(\App\Enums\InventoryStatus::fromUptownStatus('unsold'))
        ->toBe(\App\Enums\InventoryStatus::Available);
});
