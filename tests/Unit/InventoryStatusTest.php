<?php

use App\Enums\HoldType;
use App\Enums\InventoryStatus;

it('treats listing cards as templates and physical units as stock', function () {
    expect(InventoryStatus::fromUptownStatus('available'))->toBe(InventoryStatus::Available)
        ->and(InventoryStatus::fromUptownStatus('reserved'))->toBe(InventoryStatus::Reserved)
        ->and(InventoryStatus::fromUptownStatus('sold'))->toBe(InventoryStatus::Sold)
        ->and(InventoryStatus::Sold->toUptownStatus())->toBe('sold')
        ->and(InventoryStatus::Held->toUptownStatus())->toBe('reserved')
        ->and(InventoryStatus::fromUptownStatus('unsold'))->toBe(InventoryStatus::Available)
        ->and(InventoryStatus::Sold->blocksOtherSale())->toBeTrue()
        ->and(InventoryStatus::Available->blocksOtherSale())->toBeFalse();
});

it('walks stock from available through handed over', function () {
    expect(InventoryStatus::Available->canTransitionTo(InventoryStatus::Held))->toBeTrue()
        ->and(InventoryStatus::Available->canTransitionTo(InventoryStatus::Sold))->toBeTrue()
        ->and(InventoryStatus::Held->canTransitionTo(InventoryStatus::Reserved))->toBeTrue()
        ->and(InventoryStatus::Reserved->canTransitionTo(InventoryStatus::Contracted))->toBeTrue()
        ->and(InventoryStatus::Contracted->canTransitionTo(InventoryStatus::Sold))->toBeTrue()
        ->and(InventoryStatus::Sold->canTransitionTo(InventoryStatus::HandedOver))->toBeTrue()
        ->and(InventoryStatus::Sold->canTransitionTo(InventoryStatus::Available))->toBeFalse()
        ->and(HoldType::Eoi->inventoryStatus())->toBe(InventoryStatus::Held)
        ->and(HoldType::Reservation->inventoryStatus())->toBe(InventoryStatus::Reserved);
});
