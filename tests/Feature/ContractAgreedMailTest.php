<?php

use App\Mail\ContractAgreedMail;
use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\User;
use Illuminate\Support\Carbon;

it('renders the contract agreement email with the full contract copy', function () {
    $user = new User([
        'first_name' => 'Ali',
        'last_name' => 'Hassan',
        'email' => 'ali@example.com',
    ]);

    $contract = new Contract([
        'title' => 'Broker Partnership Terms',
        'pages' => [
            'The broker agrees to represent the company in good faith.',
            'Commission is paid after the deal is closed.',
        ],
    ]);
    $contract->id = 7;

    $agreement = new ContractAgreement([
        'contract_id' => 7,
        'user_id' => 1,
    ]);
    $agreement->id = 42;
    $agreement->created_at = Carbon::parse('2026-08-30 16:05:00', 'Africa/Cairo');

    $mailable = new ContractAgreedMail($user, $contract, $agreement);

    $mailable->assertSeeInHtml('Broker Partnership Terms');
    $mailable->assertSeeInHtml('Ali Hassan');
    $mailable->assertSeeInHtml('The broker agrees to represent the company in good faith.');
    $mailable->assertSeeInHtml('Commission is paid after the deal is closed.');
    $mailable->assertSeeInHtml('#42');
    $mailable->assertSeeInText('Broker Partnership Terms');
    expect($mailable->attachments())->toHaveCount(1);

    $pdf = $mailable->attachments()[0];
    expect($pdf->as)->toEndWith('.pdf');
});

it('renders the contract agreement email in Arabic', function () {
    app()->setLocale('ar');

    $user = new User([
        'first_name' => 'علي',
        'last_name' => 'حسن',
        'email' => 'ali@example.com',
    ]);

    $contract = new Contract([
        'title' => 'عقد الشراكة',
        'pages' => ['يلتزم الوسيط بتمثيل الشركة بحسن نية.'],
    ]);
    $contract->id = 3;

    $agreement = new ContractAgreement();
    $agreement->id = 9;
    $agreement->created_at = now();

    $mailable = new ContractAgreedMail($user, $contract, $agreement);

    $mailable->assertSeeInHtml('تم تأكيد الموافقة على العقد');
    $mailable->assertSeeInHtml('عقد الشراكة');
    $mailable->assertSeeInHtml('يلتزم الوسيط بتمثيل الشركة بحسن نية.');
    expect($mailable->render())->toContain('dir="rtl"')->and($mailable->render())->toContain('lang="ar"');
});
