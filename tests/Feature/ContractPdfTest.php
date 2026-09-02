<?php

use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\User;
use App\Services\ContractPdfGenerator;
use Illuminate\Support\Carbon;

it('generates a downloadable pdf copy of the contract', function () {
    $user = new User([
        'first_name' => 'Ali',
        'last_name' => 'Hassan',
        'email' => 'ali@example.com',
    ]);

    $contract = new Contract([
        'title' => 'Broker Partnership Terms',
        'pages' => ['The broker agrees to represent the company in good faith.'],
    ]);
    $contract->id = 7;

    $agreement = new ContractAgreement();
    $agreement->id = 42;
    $agreement->created_at = Carbon::parse('2026-08-30 16:05:00', 'Africa/Cairo');

    $pdf = app(ContractPdfGenerator::class)->generate($user, $contract, $agreement);

    expect($pdf['filename'])->toEndWith('.pdf')
        ->and($pdf['contents'])->toStartWith('%PDF');
});
