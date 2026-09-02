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

        // Route::middleware('permission:view-leads')
        //     ->resource('/leads', LeadController::class);

        Route::middleware('permission:view-uptowns')
            ->resource('/uptowns', UptownController::class);

        Route::middleware('permission:view-uptown-types')
            ->resource('/uptown-types', UptownTypeController::class);

        Route::middleware('permission:view-unit-sub-types')
            ->resource('/unit-sub-types', UnitSubTypeController::class);

        Route::middleware('permission:view-developers')
            ->resource('/developers', DeveloperController::class);

        Route::middleware('permission:view-deals')
            ->resource('/deals', DealController::class);

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

