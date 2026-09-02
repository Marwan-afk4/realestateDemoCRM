<?php

namespace App\Models;

use App\Enums\CommissionSplitRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionSplit extends Model
{
    protected $fillable = [
        'commission_id',
        'user_id',
        'role',
        'percentage',
        'amount',
    ];

    protected $casts = [
        'role' => CommissionSplitRole::class,
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
