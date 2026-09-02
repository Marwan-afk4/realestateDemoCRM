<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $table = 'api_logs';

    protected $fillable = [
        'path',
        'method',
        'user_id',
    ];

    protected $appends = [
        'title',
    ];

    public $timestamps = false;

    public function getTitleAttribute()
    {
        // Normalize path: replace numeric segments with {id}
        // e.g. api/user/contract/5/agree -> api/user/contract/{id}/agree
        $normalizedPath = preg_replace('/\/\d+/', '/{id}', $this->path);

        $mappings = [
            'api/login' => 'User Login',
            'api/register' => 'User Registration',
            'api/register/phone' => 'User Registration OTP Send',
            'api/register/phone/verify' => 'User Registration OTP Verify',
            'api/forgot-password' => 'User Forgot Password OTP Send',
            'api/forgot-password/verify' => 'User Forgot Password OTP Verify',
            'api/login/google' => 'Google Login Callback',
            
            'api/user/profile' => 'User Get Profile',
            'api/user/update-profile' => 'User Update Profile',
            'api/user/delete-profile' => 'User Delete Profile',
            'api/user/homepage' => 'User Get Homepage Data',
            
            'api/user/contracts' => 'User Get Contracts',
            'api/user/contract/{id}' => 'User Get Contract Details',
            'api/user/contract/{id}/agree' => 'User Agree to Contract',
            'api/user/contract/{id}/pdf' => 'User Download Contract PDF',
            'api/user/agreed-contracts' => 'User Get Agreed Contracts',
            
            'api/user/developers' => 'User Get Developers',
            'api/user/compounds/{id}' => 'User Get Developer Compounds',
            'api/user/GetCompounds-Commission' => 'User Get Compounds Commission',
            'api/user/plans-payment-method' => 'User Get Plans & Payment Methods',
            
            'api/user/send-training-request' => 'User Submit Training Request',
            'api/user/send-complaint' => 'User Submit Complaint',
            'api/user/send-deal' => 'User Submit Deal',
            'api/user/get-developer-ids' => 'User Get Developers List',
            'api/user/get-compound-ids/{id}' => 'User Get Compounds List',
            
            'api/user/add-lead' => 'User Create Lead',
            'api/user/make-payment' => 'User Submit Payment',
            'api/user/brocker-leads' => 'User Get Profits & Leads',
            'api/user/deals-done' => 'User Get Completed Deals',
            'api/user/profit-sales' => 'User Get Profit Sales',
            
            'api/user/favourites' => 'User Get Favourites',
            'api/user/Unitfavourite/{id}' => 'User Toggle Unit Favourite',
            'api/user/Compoundfavourite/{id}' => 'User Toggle Compound Favourite',
            
            'api/user/uptown-types' => 'User Get Unit Types',
            'api/user/units' => 'User Get All Units',
            'api/user/buy-units' => 'User Get Buy Units',
            'api/user/rent-units' => 'User Get Rent Units',
            'api/user/compounds-with-commission' => 'User Get Compounds with Commission',
            
            'api/user/sell-requests' => 'User Get/Create Sell Requests',
            'api/user/sell-requests/{id}' => 'User Get Sell Request Details',
            'api/user/unit-sell-request/{id}/delivery-date' => 'User Update Sell Request Delivery Date',
            'api/user/sell-requests/sub-types/{id}' => 'User Get Sub-types for Unit Type',
            
            'api/user/apartment-installments' => 'User Get/Submit Installment Requests',
            
            'api/bot/start' => 'Chatbot Start Session',
            'api/bot/select/{id}' => 'Chatbot Option Selection',

            // Admin Routes
            'api/admin/homepage' => 'Admin Get Homepage Stats',
            'api/admin/most-plan' => 'Admin Get Popular Plans',
            'api/admin/ads' => 'Admin Get Ads List',
            'api/admin/ads/add' => 'Admin Create Ad',
            'api/admin/ads/delete/{id}' => 'Admin Delete Ad',
            
            'api/admin/users' => 'Admin Get Users List',
            'api/admin/users/delete/{id}' => 'Admin Delete User',
            'api/admin/admins' => 'Admin Get Admins List',
            'api/admin/admins/add' => 'Admin Create Admin',
            'api/admin/admins/delete/{id}' => 'Admin Delete Admin',
            
            'api/admin/developers' => 'Admin Get Developers',
            'api/admin/developer/{id}' => 'Admin Get Developer Details',
            'api/admin/developer/add' => 'Admin Create Developer',
            'api/admin/developer/delete/{id}' => 'Admin Delete Developer',
            'api/admin/developer/update/{id}' => 'Admin Update Developer',
            
            'api/admin/units/{id}' => 'Admin Get Units in Compound',
            'api/admin/units/add/{id}' => 'Admin Create Unit',
            'api/admin/units/delete/{id}' => 'Admin Delete Unit',
            'api/admin/units/update/{id}' => 'Admin Update Unit',
            'api/admin/unit/image/delete/{id}' => 'Admin Delete Unit Image',
            
            'api/admin/requests/training' => 'Admin Get Training Requests',
            'api/admin/request/training/accept/{id}' => 'Admin Accept Trainer',
            'api/admin/requests/complaints' => 'Admin Get Complaints',
            'api/admin/complaint/close/{id}' => 'Admin Close Complaint',
            
            'api/admin/marketing-agency' => 'Admin Get Marketing Agencies',
            'api/admin/marketing-agency/add' => 'Admin Create Marketing Agency',
            'api/admin/marketing-agency/delete/{id}' => 'Admin Delete Marketing Agency',
            
            'api/admin/compounds' => 'Admin Get Compounds',
            'api/admin/compound/add' => 'Admin Create Compound',
            'api/admin/compound/delete/{id}' => 'Admin Delete Compound',
            
            'api/admin/plans' => 'Admin Get Subscription Plans',
            'api/admin/plan/add' => 'Admin Create Subscription Plan',
            'api/admin/plan/update/{id}' => 'Admin Update Subscription Plan',
            'api/admin/plan/delete/{id}' => 'Admin Delete Subscription Plan',
            
            'api/admin/subscribtion' => 'Admin Get Subscribers',
            'api/admin/subscribtion/delete/{id}' => 'Admin Delete Subscriber',
            
            'api/admin/payment-methods' => 'Admin Get Payment Methods',
            'api/admin/payment-method/add' => 'Admin Create Payment Method',
            'api/admin/payment-method/delete/{id}' => 'Admin Delete Payment Method',
            
            'api/admin/payments/create-subscription' => 'Admin Create Subscription Payment',
            'api/admin/payments/pending-payments' => 'Admin Get Pending Payments',
            'api/admin/payments/history-payments' => 'Admin Get Payments History',
            'api/admin/payments/approve-payment/{id}' => 'Admin Approve Payment',
            
            'api/admin/leads' => 'Admin Get Leads',
            'api/admin/lead/add' => 'Admin Create Lead',
            'api/admin/lead/delete/{id}' => 'Admin Delete Lead',
            'api/admin/lead/ids' => 'Admin Get Lead IDs',
            'api/admin/broker/add-leads/{id}' => 'Admin Assign Leads to Broker',
            
            'api/admin/broker/leads/{id}' => 'Admin Get Broker Leads',
            'api/admin/deals/make-deal' => 'Admin Make Deal',
            'api/admin/deals/Alldeals' => 'Admin Get All Deals',
            'api/admin/deals/semidone-deal/{id}' => 'Admin Semi-Done Deal',
            'api/admin/deals/accept-deal/{dealid}/{brokerId}/{developerId}/{unitId}/{leadid}/{compoundid}' => 'Admin Approve Deal',
            'api/admin/deals/leadbrockers' => 'Admin Get Lead Brokers',
            'api/admin/deals/edit-period-days/{id}' => 'Admin Edit Deal Period Days',
            'api/admin/deals/reject-deal/{id}' => 'Admin Reject Deal',
            
            'api/admin/files' => 'Admin Get Uploaded Files',
            'api/admin/file/upload' => 'Admin Upload File',
            'api/admin/file/delete/{id}' => 'Admin Delete File',
            
            'api/admin/contracts' => 'Admin Get Contracts',
            'api/admin/contract/add' => 'Admin Create Contract',
            'api/admin/contract/update/{id}' => 'Admin Update Contract',
            'api/admin/contract/delete/{id}' => 'Admin Delete Contract',
            
            'api/admin/how-to-use-vedios' => 'Admin Get Videos',
            'api/admin/how-to-use-vedio/upload' => 'Admin Upload Video',
            'api/admin/how-to-use-vedio/delete/{id}' => 'Admin Delete Video',
        ];

        return $mappings[$normalizedPath] ?? $this->path;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
