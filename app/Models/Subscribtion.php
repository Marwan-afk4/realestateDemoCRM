<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribtion extends Model
{


    protected $fillable = [
        'brocker_id',
        'start_date',
        'end_date',
    ];

    public function brocker()
    {
        return $this->belongsTo(Brocker::class);
    }


}
