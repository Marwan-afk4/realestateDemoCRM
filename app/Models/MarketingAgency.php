<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAgency extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'email',
        'phone',
        'start_date',
        'end_date',
        'image',
        'total_leads',
    ];

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function leads(){
        return $this->hasMany(Lead::class);
    }

    public function agents()
    {
        return $this->hasMany(User::class, 'marketing_agency_id')->where('role', 'agency');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'marketing_agency_id');
    }
}
