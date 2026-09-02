<?php

use App\Models\SellRequest;
use App\Models\UnitSubType;
use App\Models\UptownType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

/**
 * One-time safe schema reset for MySQL + per-test transactions.
 * Avoids RefreshDatabase's multi-table DROP which fails on half-migrated DBs.
 */
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

function sellRequestPayload(array $overrides = []): array
{
    $uptownType = UptownType::create([
        'name_en' => 'Apartment',
        'name_ar' => 'شقة',
        'status' => 'active',
    ]);

    $unitSubType = UnitSubType::create([
        'uptown_type_id' => $uptownType->id,
        'name_en' => 'Studio',
        'name_ar' => 'استوديو',
    ]);

    $tinyPng = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
    $tinyPdf = 'data:application/pdf;base64,JVBERi0xLjQK';

    return array_merge([
        'age' => 30,
        'identity_front_image' => $tinyPng,
        'identity_back_image' => $tinyPng,
        'country' => 'Egypt',
        'city' => 'Cairo',
        'area' => 'Nasr City',
        'detailed_pdf' => $tinyPdf,
        'price' => 1000000,
        'installments' => false,
        'uptown_type_id' => $uptownType->id,
        'unit_sub_type_id' => $unitSubType->id,
        'rooms_no' => 3,
        'bathrooms_no' => 2,
        'space' => 120,
        'finishing' => 'finished',
        'notes' => 'Some notes',
        'execution_date' => 'immediately',
    ], $overrides);
}

function actingAsApiUser(): User
{
    $user = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    Sanctum::actingAs($user);

    return $user;
}

it('stores finishing values and persists them', function (string $finishing) {
    actingAsApiUser();

    $response = $this->postJson('/api/user/sell-requests', sellRequestPayload([
        'finishing' => $finishing,
        'notes' => 'Persisted note',
    ]));

    $response->assertCreated()
        ->assertJsonPath('data.finishing', $finishing)
        ->assertJsonPath('data.notes', 'Persisted note');

    $this->assertDatabaseHas('sell_requests', [
        'finishing' => $finishing,
        'notes' => 'Persisted note',
    ]);
})->with(['finished', 'semi_finished', 'unfinished']);

it('rejects invalid finishing with 422', function () {
    actingAsApiUser();

    $response = $this->postJson('/api/user/sell-requests', sellRequestPayload([
        'finishing' => 'luxury',
    ]));

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error');
});

it('rejects missing finishing with 422', function () {
    actingAsApiUser();

    $payload = sellRequestPayload();
    unset($payload['finishing']);

    $response = $this->postJson('/api/user/sell-requests', $payload);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error');
});

it('stores null notes when omitted or empty', function () {
    actingAsApiUser();

    $omitNotes = sellRequestPayload();
    unset($omitNotes['notes']);

    $this->postJson('/api/user/sell-requests', $omitNotes)
        ->assertCreated()
        ->assertJsonPath('data.notes', null);

    $this->postJson('/api/user/sell-requests', sellRequestPayload([
        'notes' => '',
        'finishing' => 'semi_finished',
    ]))
        ->assertCreated()
        ->assertJsonPath('data.notes', null);
});

it('includes finishing and notes on GET list and show', function () {
    $user = actingAsApiUser();

    $uptownType = UptownType::create([
        'name_en' => 'Villa',
        'name_ar' => 'فيلا',
        'status' => 'active',
    ]);

    $subType = UnitSubType::create([
        'uptown_type_id' => $uptownType->id,
        'name_en' => 'Twin',
        'name_ar' => 'توين',
    ]);

    $sellRequest = SellRequest::create([
        'user_id' => $user->id,
        'age' => 28,
        'identity_front_image' => 'sell_requests/identity/front.png',
        'identity_back_image' => 'sell_requests/identity/back.png',
        'country' => 'Egypt',
        'city' => 'Giza',
        'area' => 'Dokki',
        'detailed_pdf' => 'sell_requests/pdfs/details.pdf',
        'price' => 500000,
        'installments' => false,
        'uptown_type_id' => $uptownType->id,
        'unit_sub_type_id' => $subType->id,
        'finishing' => 'unfinished',
        'notes' => 'List note',
        'execution_date' => '1_year',
    ]);

    $list = $this->getJson('/api/user/sell-requests');

    $list->assertOk()
        ->assertJsonPath('data.0.finishing', 'unfinished')
        ->assertJsonPath('data.0.notes', 'List note');

    $show = $this->getJson('/api/user/sell-requests/' . $sellRequest->id);

    $show->assertOk()
        ->assertJsonPath('data.finishing', 'unfinished')
        ->assertJsonPath('data.notes', 'List note');
});
