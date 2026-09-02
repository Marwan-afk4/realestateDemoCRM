<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyAppartmentInstallment extends Model
{
    use HasFactory;

    protected $table = 'buy_appartment_installments';

    protected $fillable = [
        'user_id',
        'apartment_id',
        'age',
        'identity_front_image',
        'identity_back_image',
        'city',
        'area',
        'job_title',
        'monthly_income',
        'monthly_installment',
        'years_of_installment',
        'deposit_percetage',
        'status',
    ];

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
    
    public $timestamps = true;

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Uptown::class, 'apartment_id');
    }

}
