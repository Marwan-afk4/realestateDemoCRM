<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{


    protected $fillable = [
        'brocker_id',
        'developer_id',
        'uptown_id',
        'commission',
    ];

    public function developer(){
        return $this->belongsTo(Developer::class);
    }

    public function uptown(){
        return $this->belongsTo(Uptown::class);
    }

    public function brocker(){
        return $this->belongsTo(Brocker::class);
    }
}
