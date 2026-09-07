<?php

use App\Models\BotMessage;
use App\Models\Brocker;
use App\Models\Compound;
use App\Models\Contract;
use App\Models\Developer;
use App\Models\HomeName;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Policy;
use App\Models\UnitSubType;
use App\Models\Uptown;
use App\Models\UptownType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    static $prepared = false;

    Storage::fake('public');

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
});

afterEach(function () {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
});

function marketplaceUser(array $overrides = []): User
{
    $user = User::factory()->create(array_merge([
        'role' => 'user',
        'status' => 'active',
        'phone' => '01033334444',
        'email' => 'marketplace-user@example.com',
        'password' => 'password',
        'first_name' => 'Market',
        'last_name' => 'User',
    ], $overrides));

    Brocker::create([
        'user_id' => $user->id,
        'profit' => 0,
        'number_of_deals' => 0,
        'comission_percentage' => 90,
    ]);

    Sanctum::actingAs($user);
    config(['crm.default_inbound_owner_user_id' => $user->id]);

    return $user->fresh();
}

function marketplaceCatalog(): array
{
    $developer = Developer::create([
        'name_en' => 'Market Dev',
        'name_ar' => 'مطور السوق',
        'email' => 'market-dev@example.com',
    ]);
    $compound = Compound::create([
        'developer_id' => $developer->id,
        'compound_name' => 'Market Compound',
        'image' => 'compounds/market.png',
        'units' => 20,
        'commission_percentage' => 8,
        'favourite' => 1,
    ]);
    $uptownType = UptownType::create([
        'name_en' => 'Market Apartment',
        'name_ar' => 'شقة السوق',
        'status' => 'active',
    ]);
    $subType = UnitSubType::create([
        'uptown_type_id' => $uptownType->id,
        'name_en' => 'Studio',
        'name_ar' => 'استوديو',
    ]);
    $listing = Uptown::create([
        'developer_id' => $developer->id,
        'compound_id' => $compound->id,
        'uptown_type_id' => $uptownType->id,
        'name_en' => 'Market 101',
        'name_ar' => 'السوق 101',
        'strat_price' => 2000000,
        'commission_price' => 160000,
        'delivery_date' => now()->addYear()->toDateString(),
        'status' => 'available',
        'type' => 'buy',
        'space' => 110,
        'bathroom' => 2,
        'bed' => 3,
        'code' => 'MKT-101',
    ]);
    $plan = Plan::create([
        'name' => 'Broker Plan',
        'count_of_leads' => 15,
        'period_in_days' => 30,
        'price' => 300,
        'price_after_discount' => 300,
    ]);
    $paymentMethod = PaymentMethod::create([
        'method_name' => 'Instapay',
        'status' => 'active',
    ]);
    $contract = Contract::create([
        'title' => 'Broker Agreement',
        'pages' => ['You agree to the commission terms.'],
    ]);
    $policy = Policy::create([
        'title' => 'App Policy',
        'content' => 'Be nice.',
    ]);
    $rootBot = BotMessage::create([
        'text' => 'Need help?',
        'response' => 'Pick an option',
    ]);
    $childBot = BotMessage::create([
        'parent_id' => $rootBot->id,
        'text' => 'Commissions',
        'response' => 'Our commission is 8%.',
    ]);
    HomeName::create([
        'name_en' => 'Home',
        'name_ar' => 'الرئيسية',
    ]);

    return compact(
        'developer',
        'compound',
        'uptownType',
        'subType',
        'listing',
        'plan',
        'paymentMethod',
        'contract',
        'policy',
        'rootBot',
        'childBot',
    );
}

it('allows guests to read public home names', function () {
    HomeName::create(['name_en' => 'Public', 'name_ar' => 'عام']);

    $this->getJson('/api/user/home-names')
        ->assertOk()
        ->assertJsonPath('status', 'success');
});

it('blocks guests from /api/user marketplace routes', function () {
    $this->getJson('/api/user/profile')->assertUnauthorized();
    $this->getJson('/api/user/homepage')->assertUnauthorized();
});

it('logs in through /api/login and issues a sanctum token', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '01033335555',
        'email' => 'api-login@example.com',
        'password' => 'password',
    ]);

    $this->postJson('/api/login', [
        'phone' => $user->phone,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'User successfully logged in')
        ->assertJsonStructure(['token', 'user']);
});

it('covers the old marketplace APIs under /api/user', function () {
    $user = marketplaceUser();
    $catalog = marketplaceCatalog();

    $this->getJson('/api/user/homepage')->assertOk()->assertJsonStructure(['ads', 'brocker']);
    $this->getJson('/api/user/profile')->assertOk()->assertJsonPath('user.id', $user->id);

    $this->putJson('/api/user/update-profile', [
        'first_name' => 'Marketed',
        'governce' => 'Cairo',
    ])->assertOk()->assertJsonPath('message', 'Profile updated successfully');

    $this->getJson('/api/user/developers')->assertOk()->assertJsonStructure(['developers']);
    $this->getJson('/api/user/compounds/'.$catalog['developer']->id)
        ->assertOk()
        ->assertJsonStructure(['compounds']);

    $this->getJson('/api/user/plans-payment-method')
        ->assertOk()
        ->assertJsonStructure(['plans', 'payment_methods']);

    $this->getJson('/api/user/contracts')->assertOk()->assertJsonStructure(['contracts']);
    $this->getJson('/api/user/contract/'.$catalog['contract']->id)
        ->assertOk()
        ->assertJsonPath('contract.id', $catalog['contract']->id);

    $this->postJson('/api/user/contract/'.$catalog['contract']->id.'/agree')
        ->assertOk()
        ->assertJsonPath('message', 'Successfully agreed to the contract');

    $this->getJson('/api/user/agreed-contracts')->assertOk()->assertJsonStructure(['agreedContracts']);
    $this->getJson('/api/user/contract/'.$catalog['contract']->id.'/pdf')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->getJson('/api/bot/start')->assertOk()->assertJsonStructure(['options']);
    $this->getJson('/api/bot/select/'.$catalog['rootBot']->id)
        ->assertOk()
        ->assertJsonPath('id', $catalog['rootBot']->id);

    $this->postJson('/api/user/send-training-request', [
        'full_name' => 'Market User',
        'email' => 'train@example.com',
        'phone' => 1011112222,
        'age' => 28,
        'qualification' => 'Bachelor',
        'governate' => 'Cairo',
        'experience_year' => 3,
    ])->assertOk()->assertJsonPath('message', 'Training Request Sent Successfully');

    $this->postJson('/api/user/send-complaint', [
        'name' => 'Market User',
        'phone' => 1011112222,
        'message' => 'The listing photos are outdated.',
    ])->assertOk()->assertJsonPath('message', 'Complaint Sent Successfully');

    $this->getJson('/api/user/get-developer-ids')->assertOk()->assertJsonStructure(['developers']);
    $this->getJson('/api/user/get-compound-ids/'.$catalog['developer']->id)
        ->assertOk()
        ->assertJsonStructure(['compounds']);

    $this->getJson('/api/user/GetCompounds-Commission')
        ->assertOk()
        ->assertJsonStructure(['compounds_commission']);

    $this->getJson('/api/user/brocker-leads')->assertOk()->assertJsonStructure(['leads']);
    $this->getJson('/api/user/deals-done')->assertOk()->assertJsonStructure(['dealsDone']);
    $this->getJson('/api/user/profit-sales')->assertOk()->assertJsonStructure(['profit', 'dealer_profit']);

    $this->postJson('/api/user/send-deal', [
        'fullname' => 'Buyer Name',
        'nationality_id' => '29001011234567',
        'phone' => '01099990000',
        'email' => 'buyer@example.com',
        'developer_id' => $catalog['developer']->id,
        'compound_id' => $catalog['compound']->id,
        'uptown_type_id' => $catalog['uptownType']->id,
        'number_of_units' => 1,
    ])->assertOk()->assertJsonPath('message', 'Deal Sent Successfully');

    $this->getJson('/api/user/profit-sales')->assertOk()->assertJsonStructure(['profit', 'dealer_profit']);

    $listedDeal = $this->postJson('/api/user/send-deal', [
        'fullname' => 'Listed Buyer',
        'nationality_id' => '29001011234567',
        'phone' => '01099990001',
        'email' => 'listed-buyer@example.com',
        'developer_id' => $catalog['developer']->id,
        'compound_id' => $catalog['compound']->id,
        'uptown_type_id' => $catalog['uptownType']->id,
        'uptown_id' => $catalog['listing']->id,
        'number_of_units' => 1,
    ])->assertOk();

    $this->assertDatabaseHas('deals', [
        'id' => $listedDeal->json('deal_id'),
        'uptown_id' => $catalog['listing']->id,
    ]);

    $this->getJson('/api/user/profit-sales')->assertOk()->assertJsonPath('dealer_profit', 16000);

    $this->postJson('/api/user/add-lead', [
        'interested_place' => 'New Cairo',
        'lead_name' => 'API Lead',
        'lead_phone' => '01066667777',
    ])->assertOk()->assertJsonPath('message', 'Lead added successfully');

    $this->postJson('/api/user/make-payment', [
        'plan_id' => $catalog['plan']->id,
        'payment_method_id' => $catalog['paymentMethod']->id,
        'receipt' => 'receipts/test.png',
    ])->assertOk();

    $this->putJson('/api/user/Compoundfavourite/'.$catalog['compound']->id, [
        'favourite' => 1,
    ])->assertOk()->assertJsonPath('message', 'Compound Favourite Successfully');

    $this->getJson('/api/user/favourites')
        ->assertOk()
        ->assertJsonStructure(['units', 'compounds']);

    $this->putJson('/api/user/Unitfavourite/'.$catalog['listing']->id, [
        'favourite' => 1,
    ])->assertOk()->assertJsonPath('message', 'Unit Favourite Successfully');

    $this->getJson('/api/user/favourites')
        ->assertOk()
        ->assertJsonPath('units.0.id', $catalog['listing']->id);

    $this->putJson('/api/user/Unitfavourite/'.$catalog['listing']->id, [
        'favourite' => 0,
    ])->assertOk();

    expect($this->getJson('/api/user/favourites')->json('units'))->toBe([]);

    $this->getJson('/api/user/uptown-types')->assertOk()->assertJsonStructure(['uptownTypes']);
    $this->getJson('/api/user/units')->assertOk()->assertJsonStructure(['units']);
    $this->getJson('/api/user/buy-units')->assertOk()->assertJsonStructure(['units']);
    $this->getJson('/api/user/rent-units')->assertOk()->assertJsonStructure(['units']);
    $this->getJson('/api/user/compounds-with-commission')
        ->assertOk()
        ->assertJsonStructure(['compounds']);

    $this->getJson('/api/user/sell-requests/sub-types/'.$catalog['uptownType']->id)
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $tinyPng = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
    $tinyPdf = 'data:application/pdf;base64,JVBERi0xLjQK';

    $sell = $this->postJson('/api/user/sell-requests', [
        'age' => 30,
        'identity_front_image' => $tinyPng,
        'identity_back_image' => $tinyPng,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'area' => 'Nasr City',
        'detailed_pdf' => $tinyPdf,
        'price' => 1000000,
        'installments' => false,
        'uptown_type_id' => $catalog['uptownType']->id,
        'unit_sub_type_id' => $catalog['subType']->id,
        'rooms_no' => 3,
        'bathrooms_no' => 2,
        'space' => 120,
        'finishing' => 'finished',
        'notes' => 'API sell request',
        'execution_date' => 'immediately',
    ])->assertCreated();

    $sellId = $sell->json('data.id');
    $this->getJson('/api/user/sell-requests')->assertOk()->assertJsonPath('status', 'success');
    $this->getJson('/api/user/sell-requests/'.$sellId)->assertOk()->assertJsonPath('data.id', $sellId);
    $this->putJson('/api/user/unit-sell-request/'.$catalog['listing']->id.'/delivery-date', [
        'delivery_date' => now()->addYears(2)->toDateString(),
    ])->assertOk()->assertJsonPath('status', 'success');

    $this->postJson('/api/user/apartment-installments', [
        'apartment_id' => $catalog['listing']->id,
        'age' => 30,
        'identity_front_image' => $tinyPng,
        'identity_back_image' => $tinyPng,
        'city' => 'Cairo',
        'area' => 'Nasr City',
        'job_title' => 'Engineer',
        'monthly_income' => 25000,
        'years_of_installment' => 10,
        'deposit_percetage' => 20,
    ])->assertCreated();

    $this->getJson('/api/user/apartment-installments')->assertOk()->assertJsonPath('status', 'success');

    $this->getJson('/api/user/policies')->assertOk()->assertJsonPath('status', 'success');
    $this->getJson('/api/user/policies/'.$catalog['policy']->id)
        ->assertOk()
        ->assertJsonPath('policy.id', $catalog['policy']->id);

    $this->postJson('/api/user/device-token', [
        'token' => 'fcm-token-test-1',
        'platform' => 'android',
    ])->assertOk();

    $this->deleteJson('/api/user/device-token', [
        'token' => 'fcm-token-test-1',
    ])->assertOk();

    $this->getJson('/api/user/home-names')->assertOk()->assertJsonPath('status', 'success');
});

it('logs out with a real sanctum token from /api/login', function () {
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
        'phone' => '01033336666',
        'email' => 'logout-user@example.com',
        'password' => 'password',
    ]);

    $token = $this->postJson('/api/login', [
        'phone' => $user->phone,
        'password' => 'password',
    ])->assertOk()->json('token');

    $this->withToken($token)
        ->deleteJson('/api/user/logout')
        ->assertOk()
        ->assertJsonPath('message', 'logged out successfully');
});

it('deletes the marketplace profile', function () {
    marketplaceUser(['phone' => '01033337777', 'email' => 'delete-me@example.com']);

    $this->deleteJson('/api/user/delete-profile')
        ->assertOk()
        ->assertJsonPath('message', 'Profile deleted successfully');
});

it('stars a unit favourite and lists it for the current user', function () {
    marketplaceUser();
    $catalog = marketplaceCatalog();

    $this->getJson('/api/user/favourites')
        ->assertOk()
        ->assertJsonPath('units', []);

    $this->putJson('/api/user/Unitfavourite/999999', [
        'favourite' => 1,
    ])->assertNotFound();

    $this->putJson('/api/user/Unitfavourite/'.$catalog['listing']->id, [
        'favourite' => 1,
    ])->assertOk();

    $this->assertDatabaseHas('favourites', [
        'uptown_id' => $catalog['listing']->id,
        'compound_id' => $catalog['compound']->id,
        'type' => 'unit',
    ]);

    $this->getJson('/api/user/favourites')
        ->assertOk()
        ->assertJsonPath('units.0.id', $catalog['listing']->id)
        ->assertJsonPath('compounds.0.id', $catalog['compound']->id);
});
