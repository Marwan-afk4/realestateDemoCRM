<?php

use App\Http\Controllers\HomeNameController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AdController,
    AuthController,
    BrockerController,
    BotMessageController,
    CompoundController,
    ContractController,
    ContractAgreementController,
    DealController,
    DeveloperController,
    HomePageController,
    LeadController,
    PaymentController,
    PaymentMethodController,
    PlanController,
    RequestsController,
    UptownController,
    UptownTypeController,
    UserController,
    ResidentialDataController,
    CommercialDataController,
    AdministrativeDataController,
    SellRequestController,
    BuyAppartmentInstallmentController,
    UnitSubTypeController,
    PushNotificationController,
};

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [HomePageController::class, 'index'])->name('home');
});

Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('do.login');
        Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('logout');
    });


    Route::middleware(['auth:sanctum','role:admin'])->prefix('admin')
    ->group(function () {

        // Admin Management (super-admin only — permission middleware)
        Route::middleware('permission:view-roles')
            ->resource('/roles', \App\Http\Controllers\AdminRoleController::class)
            ->names('admin-roles')
            ->parameters(['roles' => 'adminRole']);

        Route::middleware('permission:view-admins')
            ->resource('/admins', \App\Http\Controllers\AdminUserController::class)
            ->names('admin-users')
            ->parameters(['admins' => 'adminUser']);

        // ── Existing Resources with permission guards ──────────────────────
        Route::middleware('permission:view-users')
            ->resource('/users', UserController::class);

        Route::middleware('permission:view-brockers')
            ->resource('/brockers', BrockerController::class);

        Route::middleware('permission:view-pipeline')->group(function () {
            Route::get('/pipeline', [\App\Http\Controllers\PipelineController::class, 'index'])->name('pipeline.index');
            Route::get('/pipeline/{pipeline}', [\App\Http\Controllers\PipelineController::class, 'show'])->name('pipeline.show');
            Route::post('/pipeline/{pipeline}/stage', [\App\Http\Controllers\PipelineController::class, 'updateStage'])->name('pipeline.stage');
            Route::post('/pipeline/{pipeline}/assign', [\App\Http\Controllers\PipelineController::class, 'assign'])->name('pipeline.assign');
            Route::post('/pipeline/{pipeline}/unit', [\App\Http\Controllers\PipelineController::class, 'attachUnit'])->name('pipeline.unit');
            Route::post('/pipeline/{pipeline}/unlock', [\App\Http\Controllers\PipelineController::class, 'unlock'])->name('pipeline.unlock');
        });

        Route::middleware('permission:view-contacts')->group(function () {
            Route::resource('/contacts', \App\Http\Controllers\ContactController::class)->except(['destroy']);
            Route::post('/contacts/{contact}/pipeline', [\App\Http\Controllers\ContactController::class, 'addToPipeline'])->name('contacts.pipeline');
            Route::post('/contacts/{contact}/activities', [\App\Http\Controllers\ContactController::class, 'logActivity'])->name('contacts.activities.store');
            Route::post('/contacts/{contact}/message', [\App\Http\Controllers\ContactController::class, 'sendMessage'])->name('contacts.message');
        });

        Route::middleware('permission:view-crm-tasks')->group(function () {
            Route::get('/crm-tasks', [\App\Http\Controllers\CrmTaskController::class, 'index'])->name('crm-tasks.index');
            Route::post('/crm-tasks', [\App\Http\Controllers\CrmTaskController::class, 'store'])->name('crm-tasks.store');
            Route::post('/crm-tasks/{crm_task}/complete', [\App\Http\Controllers\CrmTaskController::class, 'complete'])->name('crm-tasks.complete');
        });

        Route::middleware('permission:view-message-templates')->group(function () {
            Route::resource('/message-templates', \App\Http\Controllers\MessageTemplateController::class)->except(['show']);
            Route::resource('/crm-broadcasts', \App\Http\Controllers\CrmBroadcastController::class)->only(['index', 'create', 'store']);
        });

        Route::middleware('permission:view-crm-reports')
            ->get('/sales-reports', [\App\Http\Controllers\SalesReportController::class, 'index'])
            ->name('sales-reports.index');

        Route::middleware('permission:view-leads')
            ->resource('/leads', LeadController::class);

        Route::middleware('permission:view-uptowns')
            ->resource('/uptowns', UptownController::class);

        Route::middleware('permission:view-uptown-types')
            ->resource('/uptown-types', UptownTypeController::class);

        Route::middleware('permission:view-unit-sub-types')
            ->resource('/unit-sub-types', UnitSubTypeController::class);

        Route::middleware('permission:view-developers')
            ->resource('/developers', DeveloperController::class);

        Route::middleware('permission:view-deals')->group(function () {
            Route::resource('/deals', DealController::class);
            Route::post('/deals/{deal}/hold', [\App\Http\Controllers\DealSaleController::class, 'hold'])->name('deals.hold');
            Route::post('/deals/{deal}/offers', [\App\Http\Controllers\DealSaleController::class, 'offer'])->name('deals.offers.store');
            Route::post('/deals/{deal}/offers/{sale_offer}/accept', [\App\Http\Controllers\DealSaleController::class, 'acceptOffer'])->name('deals.offers.accept');
            Route::post('/deals/{deal}/documents', [\App\Http\Controllers\DealSaleController::class, 'document'])->name('deals.documents.store');
            Route::post('/deals/{deal}/payment-plan', [\App\Http\Controllers\DealSaleController::class, 'paymentPlan'])->name('deals.payment-plan.store');
            Route::post('/deals/{deal}/installments/{buyer_installment}/receipts', [\App\Http\Controllers\DealSaleController::class, 'receipt'])->name('deals.receipts.store');
            Route::post('/deals/{deal}/handover', [\App\Http\Controllers\DealSaleController::class, 'handover'])->name('deals.handover');
            Route::post('/deals/{deal}/payout', [\App\Http\Controllers\DealSaleController::class, 'payout'])->name('deals.payout');
        });

        Route::middleware('permission:view-inventory')->group(function () {
            Route::resource('/inventory-units', \App\Http\Controllers\InventoryUnitController::class)->except(['destroy']);
            Route::post('/inventory-units/{inventory_unit}/release', [\App\Http\Controllers\InventoryUnitController::class, 'release'])->name('inventory-units.release');
        });

        Route::middleware('permission:view-collections')->group(function () {
            Route::get('/collections', [\App\Http\Controllers\BuyerCollectionController::class, 'index'])->name('collections.index');
            Route::post('/collections/{buyer_installment}/receipts', [\App\Http\Controllers\BuyerCollectionController::class, 'receipt'])->name('collections.receipts.store');
        });

        Route::get('/sale-documents/{sale_document}', function (\App\Models\SaleDocument $sale_document) {
            abort_unless(auth()->user()?->can('view-deals'), 403);

            return view('sale-documents.show', ['document' => $sale_document->load(['deal.contact', 'inventoryUnit', 'offer'])]);
        })->name('sale-documents.show');

        Route::middleware('permission:view-home-names')
            ->resource('/home-names', HomeNameController::class);

        Route::middleware('permission:view-ads')
            ->resource('/ads', AdController::class);

        Route::middleware('permission:view-contracts')
            ->resource('/contracts', ContractController::class);

        Route::middleware('permission:view-contract-agreements')
            ->resource('/contract-agreements', ContractAgreementController::class);

        Route::middleware('permission:view-bot-messages')
            ->resource('/bot-messages', BotMessageController::class);

        Route::middleware('permission:view-policies')
            ->resource('/policies', \App\Http\Controllers\PolicyController::class);

        Route::middleware('permission:view-push-notifications')
            ->resource('/push-notifications', PushNotificationController::class)
            ->except(['edit', 'update']);

        Route::middleware('permission:view-sell-requests')->group(function () {
            Route::resource('/sell-requests', SellRequestController::class);
            Route::post('/sell-requests/{sell_request}/status', [SellRequestController::class, 'updateStatus'])->name('sell-requests.update-status');
            Route::post('/sell-requests/{sell_request}/visibility', [SellRequestController::class, 'updateVisibility'])->name('sell-requests.update-visibility');
        });

        Route::middleware('permission:view-apartment-installments')->group(function () {
            Route::resource('/apartment-installments', BuyAppartmentInstallmentController::class);
            Route::post('/apartment-installments/{apartment_installment}/status', [BuyAppartmentInstallmentController::class, 'updateStatus'])->name('apartment-installments.update-status');
        });

        Route::middleware('permission:view-requests')->group(function () {
            Route::get('/requests', [RequestsController::class, 'index'])->name('requests.index');
            Route::patch('/requests/complaint/{id}/close', [RequestsController::class, 'closeComplaint'])->name('requests.complaint.close');
            Route::patch('/requests/contract/{id}/approve', [RequestsController::class, 'approveContract'])->name('requests.contract.approve');
            Route::patch('/requests/contract/{id}/reject', [RequestsController::class, 'rejectContract'])->name('requests.contract.reject');
        });


        // Payments & Plans (no specific per-route permission)
        Route::resources([
            '/payment-methods' => PaymentMethodController::class,
            '/payments'        => PaymentController::class,
            '/plans'           => PlanController::class,
        ]);

        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        // AJAX routes
        Route::get('/uptowns/compounds-by-developer', [UptownController::class, 'getCompoundsByDeveloper'])
            ->name('uptowns.compounds-by-developer');

        // Sub-data resources (no top-level sidebar entry)
        Route::resources([
            '/residential-data'    => ResidentialDataController::class,
            '/commercial-data'     => CommercialDataController::class,
            '/administrative-data' => AdministrativeDataController::class,
            '/compounds'           => CompoundController::class,
        ]);
    });

