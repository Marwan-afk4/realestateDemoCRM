<?php

namespace App\Models;

use App\Enums\LeadStatuses;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'marketing_agency_id',
        'uptown_id',
        'interested_place',
        'brocker_id',
        'brocker_end_date',
        'lead_name',
        'lead_phone',
        'brocker_start_date',
        'brocker_end_date',
        'sales_man_name',
        'sales_man_phone',
        'status',
    ];

    protected $casts = [
        'status' => LeadStatuses::class,
    ];
    public function brocker()
    {
        return $this->belongsTo(Brocker::class);
    }

    public function uptown()
    {
        return $this->belongsTo(Uptown::class);
    }

    public function marketing_agency()
    {
        return $this->belongsTo(MarketingAgency::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function brokerLeads()
    {
        return $this->hasMany(BrokerLead::class);
    }
}
