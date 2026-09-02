<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{


    protected $fillable = [
        'place',
        'developer_id',
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
}
