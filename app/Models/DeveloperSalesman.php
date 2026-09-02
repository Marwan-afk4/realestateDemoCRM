<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperSalesman extends Model
{


    protected $fillable = [
        'developer_id',
        'sale_people_id',
        'uptown_id',
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function sale_people()
    {
        return $this->belongsTo(SalePeople::class);
    }

    public function uptown()
    {
        return $this->belongsTo(Uptown::class);
    }
}
