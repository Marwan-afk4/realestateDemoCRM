<?php

use App\Enums\CommissionSplitRole;
use App\Models\Brocker;
use App\Models\Deal;
use App\Models\User;
use App\Services\Crm\CommissionSplitService;
use Tests\TestCase;

uses(TestCase::class);

function commissionDeal(Brocker $closer, ?Brocker $lister = null): Deal
{
    $deal = new Deal;
    $deal->forceFill([
        'brocker_id' => $closer->id,
        'lister_broker_id' => $lister?->id,
    ]);
    $deal->setRelation('brocker', $closer);
    $deal->setRelation('listerBroker', $lister);

    return $deal;
}

function commissionBroker(int $id, int $userId, ?int $teamLeadId = null, string $name = 'Broker'): Brocker
{
    $user = new User;
    $user->forceFill(['id' => $userId, 'first_name' => $name, 'last_name' => 'Test']);

    $broker = new Brocker;
    $broker->forceFill(['id' => $id, 'user_id' => $userId, 'team_lead_id' => $teamLeadId]);
    $broker->setRelation('user', $user);

    $manager = null;
    if ($teamLeadId) {
        $manager = new User;
        $manager->forceFill(['id' => $teamLeadId, 'first_name' => 'Manager']);
    }
    $broker->setRelation('teamLead', $manager);

    return $broker;
}

it('gives closer one hundred percent when no lister or manager', function () {
    $closer = commissionBroker(1, 10);
    $splits = app(CommissionSplitService::class)->buildForDeal(commissionDeal($closer), 100_000);

    expect($splits)->toHaveCount(1)
        ->and($splits[0]['role'])->toBe(CommissionSplitRole::Closer)
        ->and($splits[0]['percentage'])->toBe(100.0)
        ->and($splits[0]['amount'])->toBe(100_000.0);
});

it('splits closer eighty and manager twenty when team lead exists', function () {
    $closer = commissionBroker(1, 10, 99, 'Closer');
    $splits = app(CommissionSplitService::class)->buildForDeal(commissionDeal($closer), 50_000);

    $byRole = collect($splits)->mapWithKeys(fn ($row) => [$row['role']->value => $row['percentage']]);

    expect($byRole['closer'])->toBe(80.0)
        ->and($byRole['manager'])->toBe(20.0);
});

it('splits lister closer and manager when lister differs from closer', function () {
    $closer = commissionBroker(1, 10, 99, 'Closer');
    $lister = commissionBroker(2, 20, null, 'Lister');
    $splits = app(CommissionSplitService::class)->buildForDeal(commissionDeal($closer, $lister), 10_000);

    $byRole = collect($splits)->keyBy(fn ($row) => $row['role']->value);

    expect($byRole['lister']['percentage'])->toBe(20.0)
        ->and($byRole['closer']['percentage'])->toBe(60.0)
        ->and($byRole['manager']['percentage'])->toBe(20.0)
        ->and(collect($splits)->sum('amount'))->toBe(10_000.0);
});

it('gives closer the manager share when lister exists but no team lead', function () {
    $closer = commissionBroker(1, 10, null, 'Closer');
    $lister = commissionBroker(2, 20, null, 'Lister');
    $splits = app(CommissionSplitService::class)->buildForDeal(commissionDeal($closer, $lister), 1_000);

    $byRole = collect($splits)->keyBy(fn ($row) => $row['role']->value);

    expect($byRole)->toHaveKeys(['lister', 'closer'])
        ->and($byRole['lister']['percentage'])->toBe(20.0)
        ->and($byRole['closer']['percentage'])->toBe(80.0);
});
