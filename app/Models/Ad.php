<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\trait\Translatable;

class Ad extends Model
{
    use HasFactory, Translatable;

    protected $translatable = ['title'];

    protected $fillable = [
        'title_en',
        'title_ar',
        'image',
    ];

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }


}
