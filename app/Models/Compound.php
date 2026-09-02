<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compound extends Model
{

    protected $table = 'compounds';

    protected $fillable = [
        'developer_id',
        'compound_name',
        'image',
        'units',
        'commission_percentage',
        'favourite'
    ];

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function uptwons(){
        return $this->hasMany(Uptown::class);
    }

    public function deals(){
        return $this->hasMany(Deal::class);
    }

    public function developer(){
        return $this->belongsTo(Developer::class);
    }
}
