<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\trait\Translatable;

class UptownType extends Model
{
    use HasFactory, Translatable;

    protected $table = 'uptown_types';

    protected $translatable = ['name'];

    protected $fillable = [
        'name_en',
        'name_ar',
        'status'
    ];

    public $timestamps = true;

    protected $casts = [
        'status' => ActivationStatus::class,
    ];


    public function uptowns()
    {
        return $this->hasMany(Uptown::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

}
