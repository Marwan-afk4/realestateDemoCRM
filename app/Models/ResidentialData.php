<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentialData extends Model
{
    use HasFactory;

    protected $table = 'residential_data';

    protected $fillable = [
        'field_name',
        'label_en',
        'label_ar',
        'type',
        'is_required',
        'options',
        'status',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];
}
