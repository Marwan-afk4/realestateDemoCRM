<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{


    protected $fillable = [
        'method_name',
        'image',
        'status',
    ];

    public $appends = [
        'image_url'
    ];

    protected $casts = [
        'status' => ActivationStatus::class,
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }
}
