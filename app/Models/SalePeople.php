<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePeople extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_name',
        'sale_phone',
    ];

    public function salesman(){
        return $this->hasMany(DeveloperSalesman::class);
    }
}
