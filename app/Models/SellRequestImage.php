<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellRequestImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_request_id',
        'key',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function sellRequest()
    {
        return $this->belongsTo(SellRequest::class);
    }
}
