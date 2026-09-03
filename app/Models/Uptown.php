<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\trait\Translatable;

class Uptown extends Model
{
    use HasFactory, Translatable;

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'developer_id', //3aiz ashil el coulmn da mn el production
        'compound_id',
        'uptown_type_id',
        'name_en',
        'name_ar',
        'strat_price',
        'delivery_date',
        'status',
        'space',
        'bathroom',
        'bed',
        'latitude',
        'longitude',
        'floor_plan_image',
        'master_plan_image',
        'unit_plan',
        'garden_space',
        'commission_price',
        'description_en',
        'description_ar',
        'cash',
        'installment',
        'installment_years',
        'type',
        'installment_plan',
        'installment_price',
        'code',
        'reserved_deal_id',
    ];

    public $appends = [
        'master_plan_image_url',
        'floor_plan_image_url',
        'unit_plan_url',
    ];

    public function getMasterPlanImageUrlAttribute()
    {
        return $this->master_plan_image ? asset('storage/' . $this->master_plan_image) : null;
    }

    public function getFloorPlanImageUrlAttribute()
    {
        return $this->floor_plan_image ? asset('storage/' . $this->floor_plan_image) : null;
    }

    public function getUnitPlanUrlAttribute()
    {
        return $this->unit_plan ? asset('storage/' . $this->unit_plan) : null;
    }

    public function developer(){
        return $this->belongsTo(Developer::class);
    }

    public function images(){
        return $this->hasMany(UnitsImage::class);
    }

    public function salesman(){
        return $this->hasMany(DeveloperSalesman::class);
    }

    public function unitimages(){
        return $this->hasMany(UnitsImage::class);
    }

    public function compound(){
        return $this->belongsTo(Compound::class);
    }

    public function leads(){
        return $this->hasMany(Lead::class);
    }

    public function deals(){
        return $this->hasMany(Deal::class);
    }

    public function uptownType(){
        return $this->belongsTo(UptownType::class, 'uptown_type_id');
    }

    public function reservedDeal()
    {
        return $this->belongsTo(Deal::class, 'reserved_deal_id');
    }

    public function inventoryUnits()
    {
        return $this->hasMany(InventoryUnit::class);
    }

    public function listingStatus(): InventoryStatus
    {
        return InventoryStatus::fromUptownStatus($this->status);
    }
}
