<?php

use App\Enums\ContactSource;
use App\Enums\CrmTaskType;
use App\Enums\LostReason;
use App\Enums\PipelineStage;
use App\Models\Contact;
use App\Models\CrmTask;
use App\Models\PipelineTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    static $prepared = false;

    if (! $prepared) {
        $database = config('database.connections.'.config('database.default').'.database');
        if ($database !== 'delar_testing') {
            throw new RuntimeException("Refusing to reset non-testing database [{$database}]. Expected delar_testing.");
        }

        Schema::disableForeignKeyConstraints();
        foreach (Schema::getTableListing() as $table) {
            DB::statement("DROP TABLE IF EXISTS `{$table}`");
        }
        Schema::enableForeignKeyConstraints();

        $this->artisan('migrate', ['--force' => true]);
        $prepared = true;
    }

    DB::beginTransaction();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

afterEach(function () {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
});

function crmApiUser(array $overrides = []): User
{
    $user = User::factory()->create(array_merge([
        'role' => 'user',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
    ], $overrides));

    $user->ensureDefaultRole();

    Sanctum::actingAs($user);

    return $user->fresh();
}

it('lists crm lookups for an authenticated broker', function () {
    crmApiUser();

    $this->getJson('/api/crm/lookups')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure(['data' => ['sources', 'stages', 'lost_reasons', 'task_types']]);
});

it('creates a contact, opens a pipeline ticket, and moves the stage', function () {
    $user = crmApiUser();

    $create = $this->postJson('/api/crm/contacts', [
        'name' => 'Ahmed Hassan',
        'phone' => '01011112222',
        'source' => ContactSource::WalkIn->value,
        'preferred_area' => 'New Cairo',
        'add_to_pipeline' => true,
    ])->assertCreated();

    $contactId = $create->json('data.contact.id');
    $ticketId = $create->json('data.ticket.id');

    expect($contactId)->toBeInt()
        ->and($ticketId)->toBeInt()
        ->and(Contact::find($contactId)?->owner_id)->toBe($user->id)
        ->and(PipelineTicket::find($ticketId)?->stage)->toBe(PipelineStage::New);

    $this->getJson('/api/crm/contacts/'.$contactId)
        ->assertOk()
        ->assertJsonPath('data.name', 'Ahmed Hassan');

    $this->getJson('/api/crm/pipeline')
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/stage', [
        'stage' => PipelineStage::Contacted->value,
    ])->assertOk()
        ->assertJsonPath('data.stage', PipelineStage::Contacted->value);

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/stage', [
        'stage' => PipelineStage::Lost->value,
    ])->assertStatus(422);

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/stage', [
        'stage' => PipelineStage::Lost->value,
        'lost_reason' => LostReason::NoResponse->value,
        'lost_note' => 'No answer after three calls',
    ])->assertOk()
        ->assertJsonPath('data.stage', PipelineStage::Lost->value);
});

it('creates and completes a crm task', function () {
    $user = crmApiUser();
    $contact = Contact::factory()->create(['owner_id' => $user->id]);

    $store = $this->postJson('/api/crm/tasks', [
        'contact_id' => $contact->id,
        'type' => CrmTaskType::Call->value,
        'title' => 'Call back tomorrow',
        'due_at' => now()->addDay()->toIso8601String(),
    ])->assertCreated();

    $taskId = $store->json('data.id');
    expect(CrmTask::find($taskId)?->owner_id)->toBe($user->id);

    $this->getJson('/api/crm/tasks?filter=open')
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->postJson('/api/crm/tasks/'.$taskId.'/complete')
        ->assertOk()
        ->assertJsonPath('data.completed_at', fn ($value) => $value !== null);
});

it('rejects guests from the crm api', function () {
    $this->getJson('/api/crm/contacts')->assertUnauthorized();
    $this->getJson('/api/crm/pipeline')->assertUnauthorized();
});

function crmAdminUser(): User
{
    app(\Database\Seeders\PermissionSeeder::class)->run();

    $user = User::factory()->create([
        'role' => 'SuperAdmin',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
    ]);

    $user->ensureDefaultRole();
    Laravel\Sanctum\Sanctum::actingAs($user);

    return $user->fresh();
}

function crmDesk(): array
{
    $admin = crmAdminUser();
    $brokerUser = User::factory()->create([
        'role' => 'brocker',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
        'first_name' => 'Sara',
        'last_name' => 'Broker',
    ]);
    $brokerUser->ensureDefaultRole();
    $broker = App\Models\Brocker::create(['user_id' => $brokerUser->id, 'profit' => 0, 'number_of_deals' => 0]);

    $developer = App\Models\Developer::create([
        'name_en' => 'Palm Hills',
        'name_ar' => 'بالم هيلز',
        'email' => fake()->unique()->safeEmail(),
    ]);
    $compound = App\Models\Compound::create([
        'developer_id' => $developer->id,
        'compound_name' => 'Palm Parks',
        'image' => 'compounds/palm.png',
        'units' => 10,
    ]);
    $uptownType = App\Models\UptownType::create([
        'name_en' => 'Apartment',
        'name_ar' => 'شقة',
        'status' => 'active',
    ]);
    $listing = App\Models\Uptown::create([
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'name_en' => 'Palm 301',
        'name_ar' => 'بالم 301',
        'strat_price' => 2500000,
        'delivery_date' => now()->addYear()->toDateString(),
        'status' => 'available',
        'code' => 'PH-301',
    ]);
    $agency = App\Models\MarketingAgency::create([
        'name' => 'Delta Agency',
        'email' => fake()->unique()->safeEmail(),
        'phone' => 1012345678,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'total_leads' => 0,
    ]);

    return compact('admin', 'broker', 'brokerUser', 'developer', 'compound', 'uptownType', 'agency', 'listing');
}

it('runs the full crm api desk: contacts, pipeline, tasks, inventory, deals, collections, reports, messaging, after-sales, agency, developer portal, notifications', function () {
    $desk = crmDesk();
    ['developer' => $developer, 'compound' => $compound, 'uptownType' => $uptownType, 'broker' => $broker, 'agency' => $agency, 'listing' => $listing] = $desk;

    $this->getJson('/api/crm/lookups')->assertOk()->assertJsonPath('status', 'success');
    $this->getJson('/api/crm/notifications')->assertOk()->assertJsonPath('status', 'success');
    $this->postJson('/api/crm/notifications/read-all')->assertOk();

    $contact = $this->postJson('/api/crm/contacts', [
        'name' => 'Mona Ali',
        'phone' => '01022223333',
        'email' => 'mona@example.com',
        'source' => App\Enums\ContactSource::WalkIn->value,
        'preferred_area' => 'New Cairo',
        'budget_min' => 1000000,
        'budget_max' => 5000000,
        'uptown_type_id' => $uptownType->id,
        'intent' => 'buy',
        'add_to_pipeline' => true,
    ])->assertCreated();

    $contactId = $contact->json('data.contact.id');
    $ticketId = $contact->json('data.ticket.id');

    $this->getJson('/api/crm/contacts')->assertOk()->assertJsonPath('status', 'success');
    $this->getJson('/api/crm/contacts/'.$contactId)->assertOk()->assertJsonPath('data.name', 'Mona Ali');
    $this->putJson('/api/crm/contacts/'.$contactId, [
        'name' => 'Mona Ali',
        'phone' => '01022223333',
        'source' => App\Enums\ContactSource::Broker->value,
        'notes' => 'Hot buyer',
    ])->assertOk();

    $this->postJson('/api/crm/contacts/'.$contactId.'/activities', [
        'type' => App\Enums\ActivityType::Call->value,
        'body' => 'Called, will visit Saturday',
        'pipeline_ticket_id' => $ticketId,
    ])->assertOk();

    $this->postJson('/api/crm/contacts/'.$contactId.'/messages', [
        'channel' => App\Enums\MessageChannel::Whatsapp->value,
        'body' => 'Hello Mona, are you free tomorrow?',
    ])->assertOk()->assertJsonPath('data.channel', 'whatsapp');

    $this->postJson('/api/crm/contacts/'.$contactId.'/inbound', [
        'channel' => App\Enums\MessageChannel::Whatsapp->value,
        'body' => 'Yes, 4pm works',
    ])->assertOk();

    $this->getJson('/api/crm/contacts/'.$contactId.'/matches')->assertOk();

    $this->getJson('/api/crm/pipeline')->assertOk()->assertJsonPath('status', 'success');
    $this->getJson('/api/crm/pipeline/'.$ticketId)->assertOk()->assertJsonPath('data.id', $ticketId);

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/stage', [
        'stage' => App\Enums\PipelineStage::Qualified->value,
    ])->assertOk()->assertJsonPath('data.stage', 'qualified');

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/assign', [
        'brocker_id' => $broker->id,
        'expires_at' => now()->addDays(3)->toDateTimeString(),
    ])->assertOk();

    $unit = $this->postJson('/api/crm/inventory', [
        'uptown_id' => $listing->id,
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'phase' => 'A',
        'building' => '1',
        'floor' => '3',
        'unit_number' => '301',
        'list_price' => 2500000,
        'current_price' => 2400000,
    ])->assertCreated();
    $unitId = $unit->json('data.id');

    $this->getJson('/api/crm/inventory')->assertOk();
    $this->getJson('/api/crm/inventory/'.$unitId)->assertOk()->assertJsonPath('data.unit_number', '301');
    $this->putJson('/api/crm/inventory/'.$unitId, [
        'compound_id' => $compound->id,
        'unit_number' => '301',
        'phase' => 'A',
        'building' => '1',
        'floor' => '3',
        'list_price' => 2550000,
    ])->assertOk();

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/unit', [
        'inventory_unit_id' => $unitId,
    ])->assertOk()->assertJsonPath('data.inventory_unit.id', $unitId);

    $this->postJson('/api/crm/pipeline/'.$ticketId.'/unlock')->assertOk();

    $task = $this->postJson('/api/crm/tasks', [
        'contact_id' => $contactId,
        'pipeline_ticket_id' => $ticketId,
        'type' => App\Enums\CrmTaskType::Visit->value,
        'title' => 'Site visit',
        'due_at' => now()->addDays(2)->toIso8601String(),
    ])->assertCreated();
    $this->getJson('/api/crm/tasks')->assertOk();
    $this->getJson('/api/crm/tasks/calendar')->assertOk();
    $this->postJson('/api/crm/tasks/'.$task->json('data.id').'/complete')->assertOk();

    $deal = $this->postJson('/api/crm/deals', [
        'fullname' => 'Mona Ali',
        'nationality_id' => '29001011234567',
        'phone' => '01022223333',
        'email' => 'mona@example.com',
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'number_of_units' => 1,
        'inventory_unit_id' => $unitId,
        'uptown_id' => $listing->id,
        'pipeline_ticket_id' => $ticketId,
        'brocker_id' => $broker->id,
        'value' => 2400000,
    ])->assertCreated();
    $dealId = $deal->json('data.id');

    $this->getJson('/api/crm/deals')->assertOk();
    $this->getJson('/api/crm/deals/'.$dealId)->assertOk();
    $this->putJson('/api/crm/deals/'.$dealId, [
        'fullname' => 'Mona Ali',
        'phone' => '01022223333',
        'email' => 'mona@example.com',
        'value' => 2450000,
        'probability' => 70,
    ])->assertOk();

    $this->postJson('/api/crm/deals/'.$dealId.'/hold', [
        'type' => App\Enums\HoldType::Eoi->value,
    ])->assertOk();

    $offer = $this->postJson('/api/crm/deals/'.$dealId.'/offers', [
        'list_price' => 2450000,
        'discount' => 50000,
        'payment_method' => 'installment',
        'down_payment_percent' => 20,
        'installment_count' => 8,
    ])->assertOk();
    $offerId = $offer->json('data.offer.id');

    $this->postJson('/api/crm/deals/'.$dealId.'/offers/'.$offerId.'/accept')->assertOk();
    $this->postJson('/api/crm/deals/'.$dealId.'/documents', [
        'type' => App\Enums\SaleDocumentType::SaleContract->value,
    ])->assertOk();
    $this->postJson('/api/crm/deals/'.$dealId.'/payment-plan')->assertOk();

    $installmentId = App\Models\BuyerInstallment::query()->whereHas('plan', fn ($q) => $q->where('deal_id', $dealId))->value('id');
    expect($installmentId)->not->toBeNull();

    $this->getJson('/api/crm/collections?filter=all')->assertOk();
    $this->postJson('/api/crm/deals/'.$dealId.'/installments/'.$installmentId.'/receipts', [
        'amount' => 1000,
        'reference' => 'R-1',
    ])->assertOk();
    $this->postJson('/api/crm/collections/'.$installmentId.'/receipts', [
        'amount' => 500,
        'reference' => 'R-2',
    ])->assertOk();

    $this->putJson('/api/crm/deals/'.$dealId, [
        'fullname' => 'Mona Ali',
        'phone' => '01022223333',
        'email' => 'mona@example.com',
        'status' => 'approved',
        'inventory_unit_id' => $unitId,
        'uptown_id' => $listing->id,
        'brocker_id' => $broker->id,
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'number_of_units' => 1,
        'value' => 2450000,
    ])->assertOk();

    $this->postJson('/api/crm/deals/'.$dealId.'/handover')->assertOk();

    $dealModel = App\Models\Deal::find($dealId);
    if ($dealModel?->commission) {
        $this->postJson('/api/crm/deals/'.$dealId.'/payout', [
            'payout_status' => App\Enums\CommissionPayoutStatus::Paid->value,
        ])->assertOk();
    }

    $template = $this->postJson('/api/crm/message-templates', [
        'name' => 'Follow up',
        'channel' => App\Enums\MessageChannel::Whatsapp->value,
        'body' => 'Hello {{name}}',
        'is_active' => true,
    ])->assertCreated();
    $templateId = $template->json('data.id');
    $this->getJson('/api/crm/message-templates')->assertOk();
    $this->getJson('/api/crm/message-templates/'.$templateId)->assertOk();
    $this->putJson('/api/crm/message-templates/'.$templateId, [
        'name' => 'Follow up v2',
        'channel' => App\Enums\MessageChannel::Whatsapp->value,
        'body' => 'Hi {{name}}',
        'is_active' => true,
    ])->assertOk();

    $broadcast = $this->postJson('/api/crm/broadcasts', [
        'title' => 'Weekend promo',
        'channel' => App\Enums\MessageChannel::Whatsapp->value,
        'body' => 'See you at the compound',
        'message_template_id' => $templateId,
    ])->assertCreated();
    $this->getJson('/api/crm/broadcasts')->assertOk();
    $this->getJson('/api/crm/broadcasts/'.$broadcast->json('data.id'))->assertOk();

    $this->deleteJson('/api/crm/message-templates/'.$templateId)->assertOk();

    $after = $this->postJson('/api/crm/after-sales', [
        'deal_id' => $dealId,
        'type' => App\Enums\AfterSalesTicketType::CustomerCare->value,
        'priority' => 'normal',
        'title' => 'AC not cooling',
    ])->assertCreated();
    $afterId = $after->json('data.id');
    $this->getJson('/api/crm/after-sales')->assertOk();
    $this->getJson('/api/crm/after-sales/'.$afterId)->assertOk();
    $this->postJson('/api/crm/after-sales/'.$afterId.'/status', [
        'status' => App\Enums\AfterSalesTicketStatus::InProgress->value,
    ])->assertOk()->assertJsonPath('data.status', 'in_progress');

    $this->getJson('/api/crm/reports')->assertOk()->assertJsonPath('status', 'success');

    $this->getJson('/api/crm/agency')->assertOk();
    $this->getJson('/api/crm/agency/matching?contact_id='.$contactId)->assertOk();
    $this->postJson('/api/crm/marketing-agencies/'.$agency->id.'/agents', [
        'first_name' => 'Nadia',
        'last_name' => 'Agent',
        'phone' => '01033334444',
        'email' => 'nadia.agent@example.com',
        'password' => 'password',
    ])->assertCreated();
    $this->assertDatabaseHas('users', [
        'phone' => '01033334444',
        'role' => 'agency',
        'marketing_agency_id' => $agency->id,
    ]);

    $this->getJson('/api/crm/developer-portal?developer_id='.$developer->id)->assertOk();
    $this->getJson('/api/crm/developer-portal/inventory?developer_id='.$developer->id)->assertOk();
    $this->getJson('/api/crm/developer-portal/brokers?developer_id='.$developer->id)->assertOk();
    $this->postJson('/api/crm/developer-portal/brokers?developer_id='.$developer->id, [
        'brocker_ids' => [$broker->id],
    ])->assertOk();
    $this->postJson('/api/crm/developers/'.$developer->id.'/portal-users', [
        'first_name' => 'Omar',
        'last_name' => 'Portal',
        'phone' => '01044445555',
        'email' => 'omar.portal@example.com',
        'password' => 'password',
    ])->assertCreated();
    $this->assertDatabaseHas('users', [
        'phone' => '01044445555',
        'role' => 'developer',
        'developer_id' => $developer->id,
    ]);

    $this->postJson('/api/crm/inventory/'.$unitId.'/release')->assertOk();
});

it('approves a deal on physical inventory that has no listing', function () {
    $desk = crmDesk();
    ['developer' => $developer, 'compound' => $compound, 'uptownType' => $uptownType, 'broker' => $broker] = $desk;

    $unit = $this->postJson('/api/crm/inventory', [
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'phase' => 'B',
        'building' => '2',
        'floor' => '1',
        'unit_number' => '101',
        'list_price' => 1800000,
        'current_price' => 1800000,
    ])->assertCreated();
    $unitId = $unit->json('data.id');

    expect($unit->json('data.uptown_id'))->toBeNull();

    $deal = $this->postJson('/api/crm/deals', [
        'fullname' => 'Karim Saleh',
        'nationality_id' => '29001019876543',
        'phone' => '01066667777',
        'email' => 'karim@example.com',
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'number_of_units' => 1,
        'inventory_unit_id' => $unitId,
        'brocker_id' => $broker->id,
        'value' => 1800000,
    ])->assertCreated();
    $dealId = $deal->json('data.id');

    $this->putJson('/api/crm/deals/'.$dealId, [
        'fullname' => 'Karim Saleh',
        'phone' => '01066667777',
        'email' => 'karim@example.com',
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'number_of_units' => 1,
        'inventory_unit_id' => $unitId,
        'brocker_id' => $broker->id,
        'value' => 1800000,
        'status' => 'approved',
    ])->assertOk();

    $this->assertDatabaseHas('commissions', [
        'deal_id' => $dealId,
        'inventory_unit_id' => $unitId,
        'developer_id' => $developer->id,
    ]);
    expect(App\Models\Commission::query()->where('deal_id', $dealId)->value('uptown_id'))->toBeNull();
});

it('serves every dashboard index and create page for a panel admin', function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);

    $admin = User::factory()->create([
        'role' => 'admin',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
    ]);
    $admin->assignRole('super-admin');
    $this->actingAs($admin);

    App\Models\Developer::create([
        'name_en' => 'Dashboard Dev',
        'name_ar' => 'مطور',
        'email' => fake()->unique()->safeEmail(),
    ]);
    App\Models\MarketingAgency::create([
        'name' => 'Dashboard Agency',
        'email' => fake()->unique()->safeEmail(),
        'phone' => 1099988877,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'total_leads' => 0,
    ]);

    $skip = [
        'login',
        'logout',
        'uptowns.compounds-by-developer',
        'crm-tasks.calendar-events',
    ];

    $routes = collect(Illuminate\Support\Facades\Route::getRoutes())
        ->filter(function ($route) use ($skip) {
            if (! in_array('GET', $route->methods(), true)) {
                return false;
            }
            $uri = $route->uri();
            $name = $route->getName();
            if (in_array($name, $skip, true)) {
                return false;
            }
            if (str_contains($uri, '{')) {
                return false;
            }
            if (str_starts_with($uri, 'admin') || $uri === '/' || $name === 'home') {
                return true;
            }

            return false;
        })
        ->map(fn ($route) => '/'.$route->uri())
        ->unique()
        ->values();

    expect($routes->count())->toBeGreaterThan(20);

    $failures = [];
    foreach ($routes as $uri) {
        $status = $this->get($uri)->status();
        if (! in_array($status, [200, 302], true)) {
            $excerpt = substr(strip_tags($this->get($uri)->getContent()), 0, 400);
            $failures[] = "{$uri} => {$status} :: {$excerpt}";
        }
    }

    if ($failures !== []) {
        dump($failures);
    }

    expect($failures)->toBeEmpty();
});
