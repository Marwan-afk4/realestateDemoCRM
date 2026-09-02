<?php

namespace App\Models;

use App\Enums\CommissionPayoutStatus;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{


    protected $fillable = [
        'brocker_id',
        'developer_id',
        'uptown_id',
        'inventory_unit_id',
        'commission',
        'deal_id',
        'percentage',
        'amount',
        'closed_unit_price',
        'payout_status',
        'paid_at',
    ];

    public function developer(){
        return $this->belongsTo(Developer::class);
    }

    public function uptown(){
        return $this->belongsTo(Uptown::class);
    }

    public function brocker(){
        return $this->belongsTo(Brocker::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function inventoryUnit()
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function splits()
    {
        return $this->hasMany(CommissionSplit::class);
    }

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'closed_unit_price' => 'decimal:2',
        'payout_status' => CommissionPayoutStatus::class,
        'paid_at' => 'datetime',
    ];
}
