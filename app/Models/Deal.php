<?php

namespace App\Models;

use App\Enums\DealStatuses;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'fullname',
        'nationality_id',
        'phone',
        'email',
        'developer_id',
        'compound_id',
        'uptown_type_id',
        'number_of_units',
        'status',
        'contact_id',
        'lead_id',
        'pipeline_ticket_id',
        'brocker_id',
        'uptown_id',
        'inventory_unit_id',
        'sale_offer_id',
        'value',
        'close_date',
        'probability',
    ];

    protected $casts = [
        'status' => DealStatuses::class,
        'close_date' => 'date',
        'value' => 'decimal:2',
        'probability' => 'integer',
    ];



    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class);
    }

    public function uptownType()
    {
        return $this->belongsTo(UptownType::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function ticket()
    {
        return $this->belongsTo(PipelineTicket::class, 'pipeline_ticket_id');
    }

    public function brocker()
    {
        return $this->belongsTo(Brocker::class);
    }

    public function uptown()
    {
        return $this->belongsTo(Uptown::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function inventoryUnit()
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function activeOffer()
    {
        return $this->belongsTo(SaleOffer::class, 'sale_offer_id');
    }

    public function offers()
    {
        return $this->hasMany(SaleOffer::class);
    }

    public function saleDocuments()
    {
        return $this->hasMany(SaleDocument::class)->latest();
    }

    public function paymentPlan()
    {
        return $this->hasOne(BuyerPaymentPlan::class);
    }

    public function holds()
    {
        return $this->hasMany(UnitHold::class);
    }
}
