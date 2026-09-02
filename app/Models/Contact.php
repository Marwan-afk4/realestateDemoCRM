<?php

namespace App\Models;

use App\Enums\ContactSource;
use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'phone_e164',
        'phone_secondary',
        'email',
        'whatsapp',
        'national_id',
        'source',
        'budget_min',
        'budget_max',
        'preferred_area',
        'uptown_type_id',
        'intent',
        'payment_preference',
        'tags',
        'owner_id',
        'last_contacted_at',
        'notes',
    ];

    protected $casts = [
        'source' => ContactSource::class,
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'tags' => 'array',
        'last_contacted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $contact) {
            if ($contact->phone) {
                $e164 = PhoneNumber::toE164($contact->phone);
                $contact->phone_e164 = PhoneNumber::isValidE164($e164) ? $e164 : null;
                if (! $contact->whatsapp) {
                    $contact->whatsapp = $contact->phone_e164 ?: $contact->phone;
                }
            }
        });
    }

    public static function findByPhone(?string $phone): ?self
    {
        if (! $phone) {
            return null;
        }

        $candidates = PhoneNumber::lookupCandidates($phone);

        return static::query()
            ->where(function ($query) use ($candidates) {
                $query->whereIn('phone', $candidates)
                    ->orWhereIn('phone_e164', $candidates)
                    ->orWhereIn('whatsapp', $candidates);
            })
            ->first();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function uptownType(): BelongsTo
    {
        return $this->belongsTo(UptownType::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(PipelineTicket::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class)->latest();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function sellRequests(): HasMany
    {
        return $this->hasMany(SellRequest::class);
    }

    public function mortgageRequests(): HasMany
    {
        return $this->hasMany(BuyAppartmentInstallment::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(BuyerPaymentPlan::class);
    }

    public function tagsList(): array
    {
        return array_values(array_filter((array) $this->tags));
    }

    public function whatsappLink(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) ($this->whatsapp ?: $this->phone_e164 ?: $this->phone));

        return $digits ? 'https://wa.me/'.$digits : null;
    }

    public function telLink(): ?string
    {
        $phone = $this->phone_e164 ?: $this->phone;

        return $phone ? 'tel:'.$phone : null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $first = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1));
        $last = count($parts) > 1 ? mb_strtoupper(mb_substr(end($parts), 0, 1)) : '';

        return $first.$last;
    }

    public function avatarColor(): string
    {
        $colors = ['3874ff', '25b003', 'e5780b', 'e63757', '0097c2', '6f42c1', 'f5803e', '00a6ed'];
        $index = abs(crc32((string) $this->name)) % count($colors);

        return $colors[$index];
    }
}
