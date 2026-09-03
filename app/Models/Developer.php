<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\trait\Translatable;

class Developer extends Model
{
    use HasFactory, Translatable;

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'name_en',
        'name_ar',
        'email',
        'units',
        'total_deals',
        'total_profit',
        'deals_done',
        'start_date',
        'end_date',
        'image',
        'description_en',
        'description_ar'
    ];


    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_profit' => 'decimal:2',
        'units' => 'integer',
        'total_deals' => 'integer',
        'deals_done' => 'integer',
    ];

    public $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function places(){
        return $this->hasMany(Place::class);
    }

    public function uptowns(){
        return $this->hasMany(Uptown::class);
    }


    public function sales_developer(){
        return $this->hasMany(SalesDeveloper::class);
    }

    public function deals(){
        return $this->hasMany(Deal::class);
    }

    public function compounds(){
        return $this->hasMany(Compound::class);
    }

    public function inventoryUnits()
    {
        return $this->hasMany(InventoryUnit::class);
    }

    public function portalUsers()
    {
        return $this->hasMany(User::class);
    }

    public function authorizedBrokers()
    {
        return $this->belongsToMany(Brocker::class, 'developer_brocker');
    }

    public function afterSalesTickets()
    {
        return $this->hasMany(AfterSalesTicket::class);
    }

    // Accessor for formatted profit
    public function getFormattedProfitAttribute()
    {
        return '$' . number_format($this->total_profit ?? 0, 2);
    }

    // Accessor for completion percentage
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_deals > 0) {
            return round(($this->deals_done / $this->total_deals) * 100, 1);
        }
        return 0;
    }
}
