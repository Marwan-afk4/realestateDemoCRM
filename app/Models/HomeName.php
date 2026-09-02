<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeName extends Model
{
    use HasFactory;

    protected $table = 'home_names';

    protected $fillable = [
        'name_ar',
        'name_en'
    ];
    
    public $timestamps = true;

    
}
