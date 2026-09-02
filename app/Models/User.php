<?php

namespace App\Models;

use App\Enums\ActivationStatus;
use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public $guard_name = 'web';



    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'provider',
        'provider_id',
        'role',
        'qualification',
        'experience_year',
        'governce',
        'age',
        'plan_id',
        'google_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => ActivationStatus::class,
    ];

    protected $append = ['full_name'];

    public function getFullNameAttribute(){
        return "{$this->first_name} {$this->last_name}";
    }

    public static function findByPhone(string $phone): ?self
    {
        return static::query()
            ->whereIn('phone', PhoneNumber::lookupCandidates($phone))
            ->first();
    }

    public function setPasswordAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = null;
            return;
        }

        // Avoid double-hashing when callers already pass a bcrypt hash.
        if (is_string($value) && preg_match('/^\$2y\$/', $value)) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }

    public function complaints(){
        return $this->hasMany(Complaint::class);
    }

    public function brockers(){
        return $this->hasMany(Brocker::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function trainings(){
        return $this->hasMany(TrainingSubscription::class);
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function sellRequests(){
        return $this->hasMany(SellRequest::class);
    }

    public function buyAppartmentInstallments(){
        return $this->hasMany(BuyAppartmentInstallment::class);
    }

    public function contractAgreements(){
        return $this->hasMany(ContractAgreement::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function ownedContacts()
    {
        return $this->hasMany(Contact::class, 'owner_id');
    }

    public function pipelineTickets()
    {
        return $this->hasMany(PipelineTicket::class, 'owner_id');
    }

    public function crmTasks()
    {
        return $this->hasMany(CrmTask::class, 'owner_id');
    }

    public function broker()
    {
        return $this->hasOne(Brocker::class);
    }
}
