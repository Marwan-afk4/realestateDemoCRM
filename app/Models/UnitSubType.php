<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitSubType extends Model
{
    use HasFactory;

    protected $fillable = [
        'uptown_type_id',
        'name_en',
        'name_ar',
    ];

    protected $hidden = ['created_at','updated_at'];

    public function uptownType()
    {
        return $this->belongsTo(UptownType::class);
    }

    public function sellRequests()
    {
        return $this->hasMany(SellRequest::class);
    }
}
