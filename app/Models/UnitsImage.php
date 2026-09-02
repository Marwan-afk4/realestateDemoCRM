<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitsImage extends Model
{


    protected $fillable = [
        'uptown_id',
        'image',
    ];

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function uptown(){
        return $this->belongsTo(Uptown::class);
    }
}
