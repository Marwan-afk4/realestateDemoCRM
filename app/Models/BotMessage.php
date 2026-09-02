<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotMessage extends Model
{
    protected $fillable = ['parent_id', 'text', 'response'];

    public function parent()
    {
        return $this->belongsTo(BotMessage::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BotMessage::class, 'parent_id');
    }
}
