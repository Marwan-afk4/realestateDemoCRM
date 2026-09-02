<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDeveloper extends Model
{


    protected $fillable =[
        'developer_id',
        'sale_name',
        'sale_phone',
    ];

    public function developer(){
        return $this->belongsTo(Developer::class);
    }
}
