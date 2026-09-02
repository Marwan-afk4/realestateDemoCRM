<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable =[
        'title',
        'pages',
    ];

    protected $casts = [
        'pages' => 'array',
    ];

    protected $appends = [
        'body',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function getBodyAttribute()
    {
        if (is_array($this->pages)) {
            return implode("\n\n", $this->pages);
        }
        return '';
    }

    public function agreements()
    {
        return $this->hasMany(ContractAgreement::class);
    }
}
