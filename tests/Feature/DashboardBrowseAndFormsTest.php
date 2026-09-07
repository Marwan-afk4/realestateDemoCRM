<?php

use App\Enums\ActivityType;
use App\Enums\AfterSalesTicketType;
use App\Enums\ContactSource;
use App\Enums\CrmTaskType;
use App\Enums\MessageChannel;
use App\Enums\PipelineStage;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractAgreement;
use App\Models\CrmTask;
use App\Models\Deal;
use App\Models\HomeName;
use App\Models\InventoryUnit;
use App\Models\Lead;
use App\Models\MessageTemplate;
use App\Models\PipelineTicket;
use App\Models\Plan;
use App\Models\Policy;
use App\Models\SellRequest;
use App\Models\UnitSubType;
use App\Models\User;
use App\Models\Uptown;
use App\Models\UptownType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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

    Storage::fake('public');
    DB::beginTransaction();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

afterEach(function () {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
});

function dashboardAdmin(): User
{
    test()->seed(\Database\Seeders\PermissionSeeder::class);

    $admin = User::factory()->create([
        'role' => 'admin',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'first_name' => 'Dashboard',
        'last_name' => 'Admin',
        'status' => 'active',
    ]);
    $admin->assignRole('super-admin');
    config(['crm.default_inbound_owner_user_id' => $admin->id]);

    return $admin;
}

function dashboardCatalog(): array
{
    $developer = App\Models\Developer::create([
        'name_en' => 'Form Dev',
        'name_ar' => 'مطور نماذج',
        'email' => 'form-dev@example.com',
    ]);
    $compound = App\Models\Compound::create([
        'developer_id' => $developer->id,
        'compound_name' => 'Form Compound',
        'image' => 'compounds/form.png',
        'units' => 12,
        'commission_percentage' => 5,
    ]);
    $uptownType = UptownType::create([
        'name_en' => 'Form Apartment',
        'name_ar' => 'شقة نماذج',
        'status' => 'active',
    ]);
    $agency = App\Models\MarketingAgency::create([
        'name' => 'Form Agency',
        'email' => 'form-agency@example.com',
        'phone' => 1099911122,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'total_leads' => 0,
    ]);

    return compact('developer', 'compound', 'uptownType', 'agency');
}

it('logs in through the dashboard login form', function () {
    $admin = dashboardAdmin();

    $this->get('/login')
        ->assertOk()
        ->assertSee('Login', false);

    $this->from('/login')
        ->post('/login', [
            'phone' => $admin->phone,
            'password' => 'password',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->get('/')->assertOk();
});

it('clicks through dashboard pages and sees the screen headings', function () {
    $this->actingAs(dashboardAdmin());
    dashboardCatalog();

    $pages = [
        '/' => null,
        '/admin/contacts' => 'Contacts',
        '/admin/contacts/create' => 'Create Contact',
        '/admin/pipeline' => 'Pipeline',
        '/admin/crm-tasks' => 'Tasks',
        '/admin/leads' => 'Leads',
        '/admin/leads/create' => 'Create Lead',
        '/admin/deals' => 'Deals',
        '/admin/deals/create' => 'Create Deal',
        '/admin/inventory-units' => null,
        '/admin/inventory-units/create' => 'Add physical unit',
        '/admin/plans' => 'Plans',
        '/admin/plans/create' => 'Create Plan',
        '/admin/home-names' => 'Home Names',
        '/admin/home-names/create' => 'Create Home Name',
        '/admin/uptown-types' => 'Uptown Types',
        '/admin/uptown-types/create' => 'Create Uptown Type',
        '/admin/developers' => 'Developers',
        '/admin/developers/create' => 'Create Developer',
        '/admin/policies' => 'Create Policy',
        '/admin/policies/create' => 'Create Policy',
        '/admin/message-templates' => null,
        '/admin/message-templates/create' => 'Create Template',
        '/admin/after-sales' => 'After-sales',
        '/admin/collections' => null,
        '/admin/sales-reports' => null,
        '/admin/uptowns' => null,
        '/admin/uptowns/create' => null,
        '/admin/compounds' => null,
        '/admin/compounds/create' => null,
        '/admin/sell-requests/create' => 'Create Unit Request',
        '/admin/apartment-installments/create' => 'Create Mortgage Request',
        '/admin/contract-agreements/create' => 'Create Contract Agreement',
    ];

    $failures = [];
    foreach ($pages as $uri => $heading) {
        $response = $this->get($uri);
        if (! in_array($response->status(), [200, 302], true)) {
            $failures[] = "{$uri} => {$response->status()}";
            continue;
        }
        if ($response->status() === 200 && $heading !== null && ! str_contains($response->getContent(), $heading)) {
            $failures[] = "{$uri} missing heading [{$heading}]";
        }
    }

    if ($failures !== []) {
        dump($failures);
    }

    expect($failures)->toBeEmpty();
});

it('submits dashboard create and update forms', function () {
    $this->actingAs(dashboardAdmin());
    ['developer' => $developer, 'compound' => $compound, 'uptownType' => $seededType] = dashboardCatalog();

    $this->from('/admin/contacts/create')
        ->post('/admin/contacts', [
            'name' => 'Nour Hassan',
            'phone' => '01055556666',
            'source' => ContactSource::WalkIn->value,
            'preferred_area' => 'New Cairo',
            'add_to_pipeline' => '1',
        ])
        ->assertRedirect(route('pipeline.index'))
        ->assertSessionHas('success');

    $contact = Contact::where('phone', '01055556666')->first();
    expect($contact)->not->toBeNull();
    $ticket = PipelineTicket::where('contact_id', $contact->id)->first();
    expect($ticket)->not->toBeNull();

    $this->from(route('contacts.edit', $contact))
        ->post(route('contacts.update', $contact), [
            '_method' => 'PUT',
            'name' => 'Nour Hassan Updated',
            'phone' => '01055556666',
            'source' => ContactSource::Website->value,
            'notes' => 'Hot buyer from website',
        ])
        ->assertRedirect(route('contacts.show', $contact));

    expect($contact->fresh()->name)->toBe('Nour Hassan Updated');

    $this->from(route('contacts.show', $contact))
        ->post(route('contacts.activities.store', $contact), [
            'type' => ActivityType::Call->value,
            'body' => 'Called, will visit Saturday',
            'pipeline_ticket_id' => $ticket->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('crm_activities', [
        'contact_id' => $contact->id,
        'body' => 'Called, will visit Saturday',
    ]);

    $this->from(route('pipeline.show', $ticket))
        ->post(route('pipeline.stage', $ticket), [
            'stage' => PipelineStage::Contacted->value,
        ])
        ->assertRedirect();

    expect($ticket->fresh()->stage)->toBe(PipelineStage::Contacted);

    $this->from(route('crm-tasks.index'))
        ->post(route('crm-tasks.store'), [
            'contact_id' => $contact->id,
            'pipeline_ticket_id' => $ticket->id,
            'type' => CrmTaskType::Call->value,
            'title' => 'Call back tomorrow',
            'due_at' => now()->addDay()->toDateTimeString(),
        ])
        ->assertRedirect();

    $task = CrmTask::where('title', 'Call back tomorrow')->first();
    expect($task)->not->toBeNull();

    $this->from(route('crm-tasks.index'))
        ->post(route('crm-tasks.complete', $task))
        ->assertRedirect();

    expect($task->fresh()->completed_at)->not->toBeNull();

    $this->from('/admin/leads/create')
        ->post('/admin/leads', [
            'interested_place' => 'New Cairo',
            'lead_name' => 'Omar Saleh',
            'lead_phone' => '01077778888',
        ])
        ->assertRedirect(route('leads.index'))
        ->assertSessionHas('success');

    $lead = Lead::where('lead_phone', '01077778888')->first();
    expect($lead)->not->toBeNull();

    $this->from(route('leads.edit', $lead))
        ->post(route('leads.update', $lead), [
            '_method' => 'PUT',
            'lead_name' => 'Omar Saleh Updated',
            'lead_phone' => '01077778888',
            'interested_place' => 'October',
        ])
        ->assertRedirect(route('leads.index'));

    expect($lead->fresh()->lead_name)->toBe('Omar Saleh Updated');

    $this->from('/admin/message-templates/create')
        ->post('/admin/message-templates', [
            'name' => 'Follow up',
            'channel' => MessageChannel::Whatsapp->value,
            'body' => 'Hi {{name}}, checking in.',
            'is_active' => '1',
        ])
        ->assertRedirect(route('message-templates.index'))
        ->assertSessionHas('success');

    $template = MessageTemplate::where('name', 'Follow up')->first();
    expect($template)->not->toBeNull();

    $this->from(route('message-templates.edit', $template))
        ->post(route('message-templates.update', $template), [
            '_method' => 'PUT',
            'name' => 'Follow up v2',
            'channel' => MessageChannel::Whatsapp->value,
            'body' => 'Hi {{name}}',
            'is_active' => '1',
        ])
        ->assertRedirect(route('message-templates.index'));

    expect($template->fresh()->name)->toBe('Follow up v2');

    $this->from('/admin/plans/create')
        ->post('/admin/plans', [
            'name' => 'Starter',
            'count_of_leads' => 20,
            'period_in_days' => 30,
            'price' => 500,
        ])
        ->assertRedirect(route('plans.index'))
        ->assertSessionHas('success');

    $plan = Plan::where('name', 'Starter')->first();
    expect($plan)->not->toBeNull();

    $this->from(route('plans.edit', $plan))
        ->post(route('plans.update', $plan), [
            '_method' => 'PUT',
            'name' => 'Starter Plus',
            'count_of_leads' => 25,
            'period_in_days' => 30,
            'price' => 600,
        ])
        ->assertRedirect(route('plans.index'));

    expect($plan->fresh()->name)->toBe('Starter Plus');

    $this->from('/admin/home-names/create')
        ->post('/admin/home-names', [
            'name_en' => 'Buy',
            'name_ar' => 'شراء',
        ])
        ->assertRedirect(route('home-names.index'))
        ->assertSessionHas('success');

    $homeName = HomeName::where('name_en', 'Buy')->first();
    expect($homeName)->not->toBeNull();

    $this->from(route('home-names.edit', $homeName))
        ->post(route('home-names.update', $homeName), [
            '_method' => 'PUT',
            'name_en' => 'Buy Now',
            'name_ar' => 'اشتر الآن',
        ])
        ->assertRedirect(route('home-names.index'));

    expect($homeName->fresh()->name_en)->toBe('Buy Now');

    $this->from('/admin/uptown-types/create')
        ->post('/admin/uptown-types', [
            'name_en' => 'Villa',
            'name_ar' => 'فيلا',
            'status' => 'active',
        ])
        ->assertRedirect(route('uptown-types.index'))
        ->assertSessionHas('success');

    $uptownType = UptownType::where('name_en', 'Villa')->first();
    expect($uptownType)->not->toBeNull();

    $this->from(route('uptown-types.edit', $uptownType))
        ->post(route('uptown-types.update', $uptownType), [
            '_method' => 'PUT',
            'name_en' => 'Villa Plus',
            'name_ar' => 'فيلا بلس',
            'status' => 'active',
        ])
        ->assertRedirect(route('uptown-types.index'));

    expect($uptownType->fresh()->name_en)->toBe('Villa Plus');

    $this->from('/admin/policies/create')
        ->post('/admin/policies', [
            'title' => 'Privacy',
            'content' => 'We keep your data safe.',
        ])
        ->assertRedirect(route('policies.index'))
        ->assertSessionHas('success');

    $policy = Policy::where('title', 'Privacy')->first();
    expect($policy)->not->toBeNull();

    $this->from(route('policies.edit', $policy))
        ->post(route('policies.update', $policy), [
            '_method' => 'PUT',
            'title' => 'Privacy Policy',
            'content' => 'Updated terms.',
        ])
        ->assertRedirect(route('policies.index'));

    expect($policy->fresh()->title)->toBe('Privacy Policy');

    $this->from('/admin/developers/create')
        ->post('/admin/developers', [
            'name_en' => 'Posted Developer',
            'name_ar' => 'مطور منشور',
            'email' => 'posted-dev@example.com',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $postedDeveloper = App\Models\Developer::where('name_en', 'Posted Developer')->first();
    expect($postedDeveloper)->not->toBeNull();

    $this->from('/admin/compounds/create')
        ->post('/admin/compounds', [
            'developer_id' => $developer->id,
            'compound_name' => 'Posted Compound',
            'units' => 8,
            'commission_percentage' => 4,
            'image' => UploadedFile::fake()->image('compound.png'),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->from('/admin/uptowns/create')
        ->post('/admin/uptowns', [
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'uptown_type_id' => $seededType->id,
            'name_en' => 'Posted Unit',
            'name_ar' => 'وحدة منشورة',
            'strat_price' => 1500000,
            'delivery_date' => now()->addYear()->toDateString(),
            'status' => 'available',
            'space' => 120,
            'bathroom' => 2,
            'bed' => 3,
            'type' => 'buy',
            'cash' => '1',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $listing = Uptown::where('name_en', 'Posted Unit')->first();
    expect($listing)->not->toBeNull();

    $this->from('/admin/inventory-units/create')
        ->post('/admin/inventory-units', [
            'uptown_id' => $listing->id,
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'phase' => 'A',
            'building' => '1',
            'floor' => '3',
            'unit_number' => '301',
            'list_price' => 1500000,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $unit = InventoryUnit::where('unit_number', '301')->first();
    expect($unit)->not->toBeNull();

    $this->from('/admin/deals/create')
        ->post('/admin/deals', [
            'fullname' => 'Nour Hassan',
            'nationality_id' => '29001011234567',
            'phone' => '01055556666',
            'email' => 'nour@example.com',
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'uptown_type_id' => $seededType->id,
            'number_of_units' => 1,
            'uptown_id' => $listing->id,
            'inventory_unit_id' => $unit->id,
        ])
        ->assertRedirect(route('deals.index'))
        ->assertSessionHas('success');

    $deal = Deal::where('phone', '01055556666')->first();
    expect($deal)->not->toBeNull();

    $this->from(route('deals.edit', $deal))
        ->post(route('deals.update', $deal), [
            '_method' => 'PUT',
            'fullname' => 'Nour Hassan Deal',
            'nationality_id' => '29001011234567',
            'phone' => '01055556666',
            'email' => 'nour@example.com',
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'uptown_type_id' => $seededType->id,
            'number_of_units' => 1,
        ])
        ->assertRedirect();

    expect($deal->fresh()->fullname)->toBe('Nour Hassan Deal');

    $this->from(route('after-sales.index'))
        ->post(route('after-sales.store'), [
            'deal_id' => $deal->id,
            'contact_id' => $contact->id,
            'type' => AfterSalesTicketType::CustomerCare->value,
            'priority' => 'normal',
            'title' => 'AC not cooling',
        ])
        ->assertRedirect(route('after-sales.index'));

    $this->assertDatabaseHas('after_sales_tickets', [
        'title' => 'AC not cooling',
        'deal_id' => $deal->id,
    ]);
});

it('creates desk requests, portal users, and approves inventory-only deals', function () {
    $admin = dashboardAdmin();
    $this->actingAs($admin);
    ['developer' => $developer, 'compound' => $compound, 'uptownType' => $seededType, 'agency' => $agency] = dashboardCatalog();

    $subType = UnitSubType::create([
        'uptown_type_id' => $seededType->id,
        'name_en' => 'Studio',
        'name_ar' => 'استوديو',
    ]);
    $contract = Contract::create([
        'title' => 'Desk Agreement',
        'pages' => ['Commission terms apply.'],
    ]);
    $brokerUser = User::factory()->create([
        'role' => 'brocker',
        'phone' => '010'.fake()->unique()->numerify('########'),
        'email' => fake()->unique()->safeEmail(),
        'first_name' => 'Desk',
        'last_name' => 'Broker',
    ]);
    $broker = App\Models\Brocker::create([
        'user_id' => $brokerUser->id,
        'profit' => 0,
        'number_of_deals' => 0,
        'comission_percentage' => 5,
    ]);

    $this->get('/admin/sell-requests/create')->assertOk()->assertSee('Create Unit Request', false);
    $this->from('/admin/sell-requests/create')
        ->post('/admin/sell-requests', [
            'user_id' => $admin->id,
            'age' => 32,
            'identity_front_image' => UploadedFile::fake()->image('front.jpg'),
            'identity_back_image' => UploadedFile::fake()->image('back.jpg'),
            'country' => 'Egypt',
            'city' => 'Cairo',
            'area' => 'Maadi',
            'uptown_type_id' => $seededType->id,
            'unit_sub_type_id' => $subType->id,
            'price' => 2100000,
            'installments' => '0',
            'finishing' => 'finished',
            'execution_date' => 'immediately',
            'detailed_pdf' => UploadedFile::fake()->create('unit.pdf', 80, 'application/pdf'),
        ])
        ->assertRedirect();

    expect(SellRequest::query()->where('user_id', $admin->id)->where('city', 'Cairo')->exists())->toBeTrue();

    $this->get('/admin/apartment-installments/create')->assertOk()->assertSee('Create Mortgage Request', false);
    $this->from('/admin/apartment-installments/create')
        ->post('/admin/apartment-installments', [
            'user_id' => $admin->id,
            'age' => 34,
            'identity_front_image' => UploadedFile::fake()->image('front2.jpg'),
            'identity_back_image' => UploadedFile::fake()->image('back2.jpg'),
            'city' => 'Giza',
            'area' => 'Dokki',
            'job_title' => 'Engineer',
            'monthly_income' => 45000,
            'years_of_installment' => 10,
            'deposit_percetage' => 20,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('buy_appartment_installments', [
        'user_id' => $admin->id,
        'city' => 'Giza',
        'job_title' => 'Engineer',
    ]);

    $this->get('/admin/contract-agreements/create')->assertOk()->assertSee('Create Contract Agreement', false);
    $this->from('/admin/contract-agreements/create')
        ->post('/admin/contract-agreements', [
            'contract_id' => $contract->id,
            'user_id' => $admin->id,
        ])
        ->assertRedirect();

    expect(ContractAgreement::query()->where('contract_id', $contract->id)->where('user_id', $admin->id)->exists())->toBeTrue();

    $agentPhone = '010'.fake()->unique()->numerify('########');
    $portalPhone = '010'.fake()->unique()->numerify('########');
    $dealPhone = '010'.fake()->unique()->numerify('########');

    $this->from(route('agency.workspace'))
        ->post(route('marketing-agencies.agents.store', $agency), [
            'first_name' => 'Lina',
            'last_name' => 'Agent',
            'phone' => $agentPhone,
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'phone' => $agentPhone,
        'role' => 'agency',
        'marketing_agency_id' => $agency->id,
    ]);

    $this->from(route('developers.show', $developer))
        ->post(route('developers.portal-users.store', $developer), [
            'first_name' => 'Tamer',
            'last_name' => 'Portal',
            'phone' => $portalPhone,
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'phone' => $portalPhone,
        'role' => 'developer',
        'developer_id' => $developer->id,
    ]);

    $this->from('/admin/inventory-units/create')
        ->post('/admin/inventory-units', [
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'phase' => 'C',
            'building' => '9',
            'floor' => '2',
            'unit_number' => '202',
            'list_price' => 1750000,
        ])
        ->assertRedirect();

    $unit = InventoryUnit::where('unit_number', '202')->first();
    expect($unit)->not->toBeNull();
    expect($unit->uptown_id)->toBeNull();

    $this->from('/admin/deals/create')
        ->post('/admin/deals', [
            'fullname' => 'Hana Fathy',
            'nationality_id' => '29001017654321',
            'phone' => $dealPhone,
            'email' => fake()->unique()->safeEmail(),
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'uptown_type_id' => $seededType->id,
            'number_of_units' => 1,
            'inventory_unit_id' => $unit->id,
            'brocker_id' => $broker->id,
            'value' => 1750000,
        ])
        ->assertRedirect(route('deals.index'));

    $inventoryDeal = Deal::where('phone', $dealPhone)->first();
    expect($inventoryDeal)->not->toBeNull();

    $this->from(route('deals.edit', $inventoryDeal))
        ->post(route('deals.update', $inventoryDeal), [
            '_method' => 'PUT',
            'fullname' => 'Hana Fathy',
            'nationality_id' => '29001017654321',
            'phone' => $dealPhone,
            'email' => $inventoryDeal->email,
            'developer_id' => $developer->id,
            'compound_id' => $compound->id,
            'uptown_type_id' => $seededType->id,
            'number_of_units' => 1,
            'inventory_unit_id' => $unit->id,
            'brocker_id' => $broker->id,
            'value' => 1750000,
            'status' => 'approved',
        ])
        ->assertRedirect();

    expect($inventoryDeal->fresh()->status?->value ?? (string) $inventoryDeal->fresh()->status)->toBe('approved');
    expect(App\Models\Commission::query()->where('deal_id', $inventoryDeal->id)->value('uptown_id'))->toBeNull();
    $this->assertDatabaseHas('commissions', [
        'deal_id' => $inventoryDeal->id,
        'inventory_unit_id' => $unit->id,
    ]);
});
