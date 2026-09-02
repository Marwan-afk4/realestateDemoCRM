<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{


    protected $fillable = [
        'user_id',
        'brocker_id',
        'plan_id',
        'payment_method_id',
        'receipt',
        'status',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    public $appends = [
        'receipt_url',
    ];

    public function getReceiptUrlAttribute()
    {
        return $this->receipt ? asset('storage/' . $this->receipt) : null;
    }

    public function paymentmethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }



    public function user(){
        return $this->belongsTo(User::class);
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function brocker(){
        return $this->belongsTo(Brocker::class);
    }
}
