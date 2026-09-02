<?php

namespace App\Models;

use App\Enums\SellRequestExecutionDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'identity_front_image',
        'identity_back_image',
        'country',
        'city',
        'area',
        'developer_id',
        'compound_id',
        'uptown_type_id',
        'unit_sub_type_id',
        'detailed_pdf',
        'price',
        'installments',
        'installments_years',
        'installments_years_left',
        'installments_total_price',
        'installments_price_per_year',
        'rooms_no',
        'bathrooms_no',
        'space',
        'floor_no',
        'garden_area',
        'garden_space',
        'unit_plan',
        'video',
        'notes',
        'finishing',
        'extra_data',
        'visibility',
        'status',
        'execution_date',
        'delivery_date',
        'contact_id',
    ];

    protected $casts = [
        'installments' => 'boolean',
        'garden_area' => 'boolean',
        'extra_data' => 'array',
        'execution_date' => SellRequestExecutionDate::class,
    ];

    /**
     * Display delivery date as month/year (e.g. Aug 2026).
     */
    public function getDeliveryDateLabelAttribute(): ?string
    {
        if (! $this->delivery_date) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y-m', substr((string) $this->delivery_date, 0, 7))
                ->format('M Y');
        } catch (\Throwable $e) {
            return (string) $this->delivery_date;
        }
    }

    /**
     * First day of the delivery month for systems that need a full date.
     */
    public function deliveryDateAsFullDate(): ?string
    {
        if (! $this->delivery_date) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y-m', substr((string) $this->delivery_date, 0, 7))
                ->startOfMonth()
                ->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getIdentityFrontImageAttribute($value) {
        if($value){
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getIdentityBackImageAttribute($value) {
        if($value){
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getDetailedPdfAttribute($value) {
        if($value){
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getUnitPlanAttribute($value) {
        if($value){
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getVideoAttribute($value) {
        if ($value) {
            return asset('storage/' . $value);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    public function unitSubType()
    {
        return $this->belongsTo(UnitSubType::class);
    }

    public function images()
    {
        return $this->hasMany(SellRequestImage::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function ticket()
    {
        return $this->morphOne(PipelineTicket::class, 'ticketable');
    }
}
