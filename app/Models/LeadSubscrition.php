<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSubscrition extends Model
{

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'age',
        'governate',
        'experience_year',
        'interst_areas',
        'projects',
    ];
}
