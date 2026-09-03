<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brocker extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'plan_id',
        'profit',
        'number_of_deals',
        'deals_done',
        'comission_percentage',
        'team_lead_id',
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function leads(){
        return $this->hasMany(Lead::class);
    }

    public function brokerLeads(){
        return $this->hasMany(BrokerLead::class);
    }

    public function subscribtions(){
        return $this->hasMany(Subscribtion::class);
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function pipelineTickets()
    {
        return $this->hasMany(PipelineTicket::class);
    }

    public function authorizedDevelopers()
    {
        return $this->belongsToMany(Developer::class, 'developer_brocker');
    }
}
