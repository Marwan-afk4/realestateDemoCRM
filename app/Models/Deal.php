<?php

namespace App\Models;

use App\Enums\DealStatuses;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'fullname',
        'nationality_id',
        'phone',
        'email',
        'developer_id',
        'compound_id',
        'uptown_type_id',
        'number_of_units',
        'status'
    ];

    protected $casts = [
        'status' => DealStatuses::class,
    ];



    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class);
    }

    public function uptownType()
    {
        return $this->belongsTo(UptownType::class);
    }
}
