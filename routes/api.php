<?php

use App\Http\Controllers\Api\Admin\AdsController;
use App\Http\Controllers\Api\Admin\BrokerController;
use App\Http\Controllers\Api\Admin\CompundController;
use App\Http\Controllers\Api\Admin\ContractController;
use App\Http\Controllers\Api\Admin\DealsController;
use App\Http\Controllers\Api\Admin\DeveloperControlelr;
use App\Http\Controllers\Api\Admin\FilesController;
use App\Http\Controllers\Api\Admin\HomepageController;
use App\Http\Controllers\Api\Admin\LeadController;
use App\Http\Controllers\Api\Admin\MarketingAgencyController;
use App\Http\Controllers\Api\Admin\PaymentMethodController;
use App\Http\Controllers\Api\Admin\PaymentsController;
use App\Http\Controllers\Api\Admin\PlanController;
use App\Http\Controllers\Api\Admin\RequestsController;
use App\Http\Controllers\Api\Admin\SubscriptionController;
use App\Http\Controllers\Api\Admin\UnitController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\UsesVedioController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\AddLeadController;
use App\Http\Controllers\Api\User\BotMessageController;
use App\Http\Controllers\Api\User\CommissionController;
use App\Http\Controllers\Api\User\DeveloperController as UserDeveloperController;
use App\Http\Controllers\Api\User\FavouritesController;
use App\Http\Controllers\Api\User\HomePageController as UserHomePageController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\PolicyController;
use App\Http\Controllers\Api\User\PyamentMethodsController;
use App\Http\Controllers\Api\User\RequestsController as UserRequestsController;
use App\Http\Controllers\Api\User\ContractController as UserContractController;
use App\Http\Controllers\Api\User\DealController;
use App\Http\Controllers\Api\User\HomeNamesController;
use App\Http\Controllers\Api\User\UnitController as UserUnitController;
use App\Http\Controllers\Api\User\UserProfitController;
use App\Http\Controllers\Api\User\SellRequestController;
use App\Http\Controllers\Api\User\BuyAppartmentInstallmentController;
use App\Http\Controllers\Api\User\DeviceTokenController;
use App\Http\Controllers\Api\Crm\AfterSalesController as CrmAfterSalesController;
use App\Http\Controllers\Api\Crm\AgencyWorkspaceController as CrmAgencyWorkspaceController;
use App\Http\Controllers\Api\Crm\CollectionController as CrmCollectionController;
use App\Http\Controllers\Api\Crm\ContactController as CrmContactController;
use App\Http\Controllers\Api\Crm\CrmBroadcastController as CrmBroadcastApiController;
use App\Http\Controllers\Api\Crm\CrmTaskController as CrmTaskApiController;
use App\Http\Controllers\Api\Crm\DealController as CrmDealController;
use App\Http\Controllers\Api\Crm\DeveloperPortalController as CrmDeveloperPortalController;
use App\Http\Controllers\Api\Crm\InventoryUnitController as CrmInventoryUnitController;
use App\Http\Controllers\Api\Crm\LookupController as CrmLookupController;
use App\Http\Controllers\Api\Crm\MessageTemplateController as CrmMessageTemplateController;
use App\Http\Controllers\Api\Crm\NotificationController as CrmNotificationController;
use App\Http\Controllers\Api\Crm\PipelineController as CrmPipelineController;
use App\Http\Controllers\Api\Crm\SalesReportController as CrmSalesReportController;
use App\Http\Controllers\UptownTypeController;
use Illuminate\Support\Facades\Route;


Route::post('/register',[AuthController::class, 'register']);
Route::post('/register/phone',[AuthController::class, 'sendPhoneOtp'])->middleware('throttle:10,1');
Route::post('/register/phone/verify',[AuthController::class, 'verifyPhoneOtp'])->middleware('throttle:20,1');
Route::post('/forgot-password',[AuthController::class, 'forgotPassword'])->middleware('throttle:10,1');
Route::post('/forgot-password/verify',[AuthController::class, 'verifyForgotPassword'])->middleware('throttle:20,1');
Route::post('/login',[AuthController::class, 'login']);
// Route::middleware(['web'])->group(function () {
//     Route::get('/login/google', [AuthController::class, 'googleLogin']);
//     Route::get('/login/google-callback', [AuthController::class, 'googleAuthenticationCallback']);
// });

Route::post('/login/google', [AuthController::class, 'googleAuthenticationCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/device-token', [DeviceTokenController::class, 'store']);
    Route::delete('/user/device-token', [DeviceTokenController::class, 'destroy']);
});


Route::get('/user/home-names',[HomeNamesController::class, 'index']);


Route::middleware(['auth:sanctum', 'role:SuperAdmin',])->group(function () {

    Route::get('/admin/homepage',[HomepageController::class, 'homepage']);

    Route::get('/admin/most-plan',[HomepageController::class, 'getMostPlan']);

    Route::post('/admin/logout',[AuthController::class, 'logout']);

/////////////////////////////////////////////// ADS ////////////////////////////////////////////////////

    Route::get('/admin/ads',[AdsController::class, 'getAds']);

    Route::post('/admin/ads/add',[AdsController::class, 'addAdds']);

    Route::delete('/admin/ads/delete/{id}',[AdsController::class, 'deleteAds']);

//////////////////////////////////// Users ////////////////////////////////////////////////////

    Route::get('/admin/users',[UserController::class, 'getUsers']);

    Route::delete('/admin/users/delete/{id}',[UserController::class, 'deleteuser']);

    Route::get('/admin/admins',[UserController::class, 'getAdmins']);

    Route::post('/admin/admins/add',[UserController::class, 'addAdmin']);

    Route::delete('/admin/admins/delete/{id}',[UserController::class, 'deleteadmin']);

///////////////////////////////////////////// Developer ///////////////////////////////////////

    Route::get('/admin/developers',[DeveloperControlelr::class, 'AllDevelopers']);

    Route::get('/admin/developer/{id}',[DeveloperControlelr::class, 'developer']);

    Route::post('/admin/developer/add',[DeveloperControlelr::class, 'addDeveloper']);

    Route::delete('/admin/developer/delete/{id}',[DeveloperControlelr::class, 'deleteDeveloper']);

    Route::put('/admin/developer/update/{id}',[DeveloperControlelr::class, 'updateDeveloper']);

//////////////////////////////////////////// Uptowns (units) ////////////////////////////////////////////////

    Route::get('/admin/units/{compound_id}',[UnitController::class, 'unitDeveloper']);

    Route::post('/admin/units/add/{compound_id}',[UnitController::class, 'addUptown']);

    Route::delete('/admin/units/delete/{id}',[UnitController::class, 'deleteUptown']);

    Route::put('/admin/units/update/{id}',[UnitController::class, 'updateUptown']);

    Route::delete('/admin/unit/image/delete/{id}',[UnitController::class, 'DeleteUptownImage']);

///////////////////////////////////////////////// Requests (Training) /////////////////////////////////////////

    Route::get('/admin/requests/training',[RequestsController::class, 'getTrainingRequests']);

    Route::put('/admin/request/training/accept/{id}',[RequestsController::class, 'acceptTrainer']);

///////////////////////////////////////////// Requests (Complaints) /////////////////////////////////////////

    Route::get('/admin/requests/complaints',[RequestsController::class, 'getComplaints']);

    Route::delete('/admin/complaint/close/{id}',[RequestsController::class, 'closeComplaint']);

/////////////////////////////////////////// Marketing Agency //////////////////////////////////////////////

    Route::get('/admin/marketing-agency',[MarketingAgencyController::class, 'getMarketingAgency']);

    Route::post('/admin/marketing-agency/add',[MarketingAgencyController::class, 'addMarketagency']);

    Route::delete('/admin/marketing-agency/delete/{id}',[MarketingAgencyController::class, 'deleteMarketingAgency']);

//////////////////////////////////////////// Compounds /////////////////////////////////////////////////////////

    Route::get('/admin/compounds',[CompundController::class, 'compounds']);

    Route::post('/admin/compound/add',[CompundController::class, 'addCompound']);

    Route::delete('/admin/compound/delete/{id}',[CompundController::class, 'deleteCompound']);

///////////////////////////////////////////// Plans /////////////////////////////////////////////////////////

    Route::get('/admin/plans',[PlanController::class, 'plans']);

    Route::post('/admin/plan/add',[PlanController::class, 'addplan']);

    Route::put('/admin/plan/update/{id}',[PlanController::class, 'updateplan']);

    Route::delete('/admin/plan/delete/{id}',[PlanController::class, 'deleteplan']);

/////////////////////////////////////////////// Subscribtion ///////////////////////////////////////////////////

    Route::get('/admin/subscribtion',[SubscriptionController::class, 'getSubscribers']);

    Route::delete('/admin/subscribtion/delete/{id}',[SubscriptionController::class, 'deleteSubscribers']);

///////////////////////////////////////////////// Payment Method /////////////////////////////////////////////////////

    Route::get('/admin/payment-methods',[PaymentMethodController::class, 'getPaymentMethods']);

    Route::post('/admin/payment-method/add',[PaymentMethodController::class, 'createPaymentMethod']);

    Route::delete('/admin/payment-method/delete/{id}',[PaymentMethodController::class, 'deletePaymentMethod']);

////////////////////////////////////////////////// Pyaments /////////////////////////////////////////////////////

    Route::post('/admin/payments/create-subscription',[PaymentsController::class, 'createSubscription']);

    Route::get('/admin/payments/pending-payments',[PaymentsController::class, 'getPendingPayments']);

    Route::get('/admin/payments/history-payments',[PaymentsController::class, 'historyPayment']);

    Route::put('/admin/payments/approve-payment/{payment_id}',[PaymentsController::class, 'approvePayment']);

/////////////////////////////////////////////////// Leads //////////////////////////////////////////////////////////////////

    Route::get('/admin/leads',[LeadController::class, 'getLeads']);

    Route::delete('/admin/lead/delete/{id}',[LeadController::class, 'deleteLead']);

    Route::post('/admin/lead/add',[LeadController::class, 'AddLead']);

    Route::get('/admin/lead/ids',[LeadController::class, 'getIDS']);

    ////////////////////////////////////////////// AASSIGN LEAD TO BROKER ////////////////////////////////////////////////

    Route::post('/admin/broker/add-leads/{id}',[BrokerController::class, 'addLeadtoBroker']);

///////////////////////////////////////////////// Deals /////////////////////////////////////////////////////////////////

    Route::get('/admin/broker/leads/{id}',[DealsController::class, 'getBrokerLeads']);

    Route::post('/admin/deals/make-deal',[DealsController::class, 'makeDeal']);

    Route::get('/admin/deals/Alldeals',[DealsController::class, 'getalldeals']);

    Route::put('/admin/deals/semidone-deal/{id}',[DealsController::class, 'semidonedeal']);

    Route::put('/admin/deals/accept-deal/{dealid}/{brokerId}/{developerId}/{unitId}/{leadid}/{compoundid}',[DealsController::class, 'approveDeal']);

    Route::get('/admin/deals/leadbrockers',[DealsController::class, 'getleadbrockers']);

    Route::put('/admin/deals/edit-period-days/{id}',[DealsController::class, 'editPeriodDays']);

    Route::put('/admin/deals/reject-deal/{id}',[DealsController::class, 'rejectdeal']);

///////////////////////////////////////////// Files /////////////////////////////////////////////////////////////////

    Route::get('/admin/files',[FilesController::class, 'getallFiles']);

    Route::post('/admin/file/upload',[FilesController::class, 'addFile']);

    Route::delete('/admin/file/delete/{id}',[FilesController::class, 'deleteFile']);

////////////////////////////////////////////// Contracts /////////////////////////////////////////////////////////////

    Route::get('/admin/contracts',[ContractController::class, 'getContracts']);

    Route::post('/admin/contract/add',[ContractController::class, 'createContract']);

    Route::put('/admin/contract/update/{id}',[ContractController::class, 'updateContract']);

    Route::delete('/admin/contract/delete/{id}',[ContractController::class, 'deleteContract']);

////////////////////////////////////////////// How to use Vedios /////////////////////////////////////////////////////

    Route::get('/admin/how-to-use-vedios',[UsesVedioController::class, 'getVedios']);

    Route::post('/admin/how-to-use-vedio/upload',[UsesVedioController::class, 'uploadVideo']);

    Route::delete('/admin/how-to-use-vedio/delete/{id}',[UsesVedioController::class, 'deleteVideo']);

});







Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

        Route::get('/user/homepage',[UserHomePageController::class, 'homepage']);

        Route::delete('/user/logout',[AuthController::class, 'logout']);

        Route::get('/user/profile',[UserHomePageController::class, 'profile']);

        Route::delete('/user/delete-profile',[UserHomePageController::class, 'deleteProfile']);

        Route::put('/user/update-profile',[UserHomePageController::class, 'UpdateProfile']);

//////////////////////////////////////////////// Developer /////////////////////////////////////////////////////////

    Route::get('/user/developers',[UserDeveloperController::class, 'GetDevloper']);

    Route::get('/user/compounds/{developer_id}',[UserDeveloperController::class, 'getCompounds']);

/////////////////////////////////////////////// Plans PyamentMehtods /////////////////////////////////////////////////////

    Route::get('/user/plans-payment-method',[PyamentMethodsController::class, 'PlansPaymentMethod']);

///////////////////////////////////////////////// Contracts /////////////////////////////////////////////////////////

    Route::get('/user/contracts',[UserContractController::class, 'index']);

    Route::get('/user/contract/{id}',[UserContractController::class, 'show']);
    Route::post('/user/contract/{id}/agree',[UserContractController::class, 'agree']);
    Route::get('/user/contract/{id}/pdf',[UserContractController::class, 'downloadPdf']);
    Route::get('/user/agreed-contracts',[UserContractController::class, 'agreedContracts']);

    // Bot Messages
    Route::get('/bot/start', [BotMessageController::class, 'start']);
    Route::get('/bot/select/{id}', [BotMessageController::class, 'select']);

///////////////////////////////////////////////// Training Request /////////////////////////////////////////////////////////

    Route::post('/user/send-training-request',[UserRequestsController::class, 'sendTrainingRequest']);

///////////////////////////////////////////////// Complaint /////////////////////////////////////////////////////////

    Route::post('/user/send-complaint',[UserRequestsController::class, 'sendComplaint']);

///////////////////////////////////////////////// Deals /////////////////////////////////////////////////////////

    Route::post('/user/send-deal',[DealController::class, 'sendDeal']);

    Route::get('/user/get-developer-ids',[DealController::class, 'getDeveloperIds']);

    Route::get('/user/get-compound-ids/{developer_id}',[DealController::class, 'getCompoundIds']);

////////////////////////////////////////////////// Compounds Commission /////////////////////////////////////////////////////

    Route::get('/user/GetCompounds-Commission',[CommissionController::class, 'getAllCommission']);

    ///////////////////////////////////////////// AddLead //////////////////////////////////////////////////////////////

    Route::post('/user/add-lead',[AddLeadController::class, 'addLead']);

/////////////////////////////////////////////// USER MAKE PAYMENT /////////////////////////////////////////////////////

    Route::post('/user/make-payment',[PaymentController::class, 'makePayment']);

///////////////////////////////////////////////////  User Profit ////////////////////////////////////////////////////////////////

    Route::get('/user/brocker-leads',[UserProfitController::class, 'ProfitwithLeads']);

    Route::get('/user/deals-done',[UserProfitController::class, 'dealsDone']);

    Route::get('/user/profit-sales',[UserProfitController::class, 'Profit_Sales']);

//////////////////////////////////////////////////// Favorite ////////////////////////////////////////////////////////////////

    Route::get('/user/favourites',[FavouritesController::class, 'getFavourites']);

    Route::put('/user/Unitfavourite/{id}',[FavouritesController::class, 'unitFavourite']);

    Route::put('/user/Compoundfavourite/{id}',[FavouritesController::class, 'compoundFavourite']);

////////////////////////////////////////////////////// UptownType //////////////////////////////////////////////////////////////

    Route::get('/user/uptown-types',[UptownTypeController::class, 'getUptownTypes']);

//////////////////////////////////////////////////////// Units //////////////////////////////////////////////////////////////
    Route::get('/user/units',[UserUnitController::class, 'getUnits']);
    Route::get('/user/buy-units',[UserUnitController::class, 'getBuyUnits']);
    Route::get('/user/rent-units',[UserUnitController::class, 'getRentUnits']);
    Route::get('/user/compounds-with-commission',[UserUnitController::class, 'getCompoundswithCommission']);

////////////////////////////////////////////////////// Sell Requests /////////////////////////////////////////////////////////
    Route::get('/user/sell-requests/sub-types/{uptown_type_id}',[SellRequestController::class, 'getSubTypes']);
    Route::post('/user/sell-requests',[SellRequestController::class, 'store']);
    Route::get('/user/sell-requests',[SellRequestController::class, 'index']);
    Route::get('/user/sell-requests/{id}',[SellRequestController::class, 'show']);
    Route::put('/user/unit-sell-request/{id}/delivery-date',[SellRequestController::class, 'updateDeliveryDate']);

////////////////////////////////////////////////////// Apartment Installments ////////////////////////////////////////////////
    Route::post('/user/apartment-installments',[BuyAppartmentInstallmentController::class, 'store']);
    Route::get('/user/apartment-installments',[BuyAppartmentInstallmentController::class, 'index']);

////////////////////////////////////////////////////// Policies //////////////////////////////////////////////////////////////
    Route::get('/user/policies', [PolicyController::class, 'index']);
    Route::get('/user/policies/{id}', [PolicyController::class, 'show']);
    });

Route::middleware('auth:sanctum')->prefix('crm')->group(function () {
    Route::get('/lookups', [CrmLookupController::class, 'index']);

    Route::get('/notifications', [CrmNotificationController::class, 'index']);
    Route::post('/notifications/read-all', [CrmNotificationController::class, 'markAllRead']);
    Route::post('/notifications/{notification}/read', [CrmNotificationController::class, 'markRead']);

    Route::get('/contacts', [CrmContactController::class, 'index']);
    Route::post('/contacts', [CrmContactController::class, 'store']);
    Route::get('/contacts/{contact}', [CrmContactController::class, 'show']);
    Route::put('/contacts/{contact}', [CrmContactController::class, 'update']);
    Route::post('/contacts/{contact}/pipeline', [CrmContactController::class, 'addToPipeline']);
    Route::post('/contacts/{contact}/activities', [CrmContactController::class, 'logActivity']);
    Route::post('/contacts/{contact}/messages', [CrmContactController::class, 'sendMessage']);
    Route::post('/contacts/{contact}/inbound', [CrmContactController::class, 'logInbound']);
    Route::get('/contacts/{contact}/matches', [CrmContactController::class, 'matches']);

    Route::get('/pipeline', [CrmPipelineController::class, 'index']);
    Route::get('/pipeline/{pipeline}', [CrmPipelineController::class, 'show']);
    Route::post('/pipeline/{pipeline}/stage', [CrmPipelineController::class, 'updateStage']);
    Route::post('/pipeline/{pipeline}/assign', [CrmPipelineController::class, 'assign']);
    Route::post('/pipeline/{pipeline}/unit', [CrmPipelineController::class, 'attachUnit']);
    Route::post('/pipeline/{pipeline}/unlock', [CrmPipelineController::class, 'unlock']);

    Route::get('/tasks', [CrmTaskApiController::class, 'index']);
    Route::get('/tasks/calendar', [CrmTaskApiController::class, 'calendarEvents']);
    Route::post('/tasks', [CrmTaskApiController::class, 'store']);
    Route::post('/tasks/{crm_task}/complete', [CrmTaskApiController::class, 'complete']);

    Route::get('/deals', [CrmDealController::class, 'index']);
    Route::post('/deals', [CrmDealController::class, 'store']);
    Route::get('/deals/{deal}', [CrmDealController::class, 'show']);
    Route::put('/deals/{deal}', [CrmDealController::class, 'update']);
    Route::post('/deals/{deal}/hold', [CrmDealController::class, 'hold']);
    Route::post('/deals/{deal}/offers', [CrmDealController::class, 'offer']);
    Route::post('/deals/{deal}/offers/{sale_offer}/accept', [CrmDealController::class, 'acceptOffer']);
    Route::post('/deals/{deal}/documents', [CrmDealController::class, 'document']);
    Route::post('/deals/{deal}/payment-plan', [CrmDealController::class, 'paymentPlan']);
    Route::post('/deals/{deal}/installments/{buyer_installment}/receipts', [CrmDealController::class, 'receipt']);
    Route::post('/deals/{deal}/handover', [CrmDealController::class, 'handover']);
    Route::post('/deals/{deal}/payout', [CrmDealController::class, 'payout']);

    Route::get('/inventory', [CrmInventoryUnitController::class, 'index']);
    Route::post('/inventory', [CrmInventoryUnitController::class, 'store']);
    Route::get('/inventory/{inventory_unit}', [CrmInventoryUnitController::class, 'show']);
    Route::put('/inventory/{inventory_unit}', [CrmInventoryUnitController::class, 'update']);
    Route::post('/inventory/{inventory_unit}/release', [CrmInventoryUnitController::class, 'release']);

    Route::get('/collections', [CrmCollectionController::class, 'index']);
    Route::post('/collections/{buyer_installment}/receipts', [CrmCollectionController::class, 'receipt']);

    Route::get('/reports', [CrmSalesReportController::class, 'index']);

    Route::get('/message-templates', [CrmMessageTemplateController::class, 'index']);
    Route::post('/message-templates', [CrmMessageTemplateController::class, 'store']);
    Route::get('/message-templates/{message_template}', [CrmMessageTemplateController::class, 'show']);
    Route::put('/message-templates/{message_template}', [CrmMessageTemplateController::class, 'update']);
    Route::delete('/message-templates/{message_template}', [CrmMessageTemplateController::class, 'destroy']);

    Route::get('/broadcasts', [CrmBroadcastApiController::class, 'index']);
    Route::post('/broadcasts', [CrmBroadcastApiController::class, 'store']);
    Route::get('/broadcasts/{crm_broadcast}', [CrmBroadcastApiController::class, 'show']);

    Route::get('/after-sales', [CrmAfterSalesController::class, 'index']);
    Route::post('/after-sales', [CrmAfterSalesController::class, 'store']);
    Route::get('/after-sales/{after_sales_ticket}', [CrmAfterSalesController::class, 'show']);
    Route::post('/after-sales/{after_sales_ticket}/status', [CrmAfterSalesController::class, 'updateStatus']);

    Route::get('/agency', [CrmAgencyWorkspaceController::class, 'index']);
    Route::get('/agency/matching', [CrmAgencyWorkspaceController::class, 'matching']);
    Route::post('/marketing-agencies/{marketingAgency}/agents', [CrmAgencyWorkspaceController::class, 'storeAgent']);

    Route::get('/developer-portal', [CrmDeveloperPortalController::class, 'index']);
    Route::get('/developer-portal/inventory', [CrmDeveloperPortalController::class, 'inventory']);
    Route::get('/developer-portal/brokers', [CrmDeveloperPortalController::class, 'brokers']);
    Route::post('/developer-portal/brokers', [CrmDeveloperPortalController::class, 'syncBrokers']);
    Route::post('/developers/{developer}/portal-users', [CrmDeveloperPortalController::class, 'storePortalUser']);
});
