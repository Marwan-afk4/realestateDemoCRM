<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'age',
        'qualification',
        'governate',
        'experience_year',
        'status',
        'training_period'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
