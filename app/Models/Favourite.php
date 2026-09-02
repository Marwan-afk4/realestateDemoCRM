<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $table = 'favourites';

    protected $fillable = [
        'user_id',
        'uptown_id',
        'compound_id',
        'type'
    ];
    
    public $timestamps = true;

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uptown()
    {
        return $this->belongsTo(Uptown::class);
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class);
    }

}
