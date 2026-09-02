<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Enums\BuyerInstallmentStatus;
use App\Enums\InventoryStatus;
use App\Enums\SaleDocumentType;
use App\Enums\SaleOfferStatus;
use App\Models\BuyerInstallment;
use App\Models\BuyerPaymentPlan;
use App\Models\BuyerReceipt;
use App\Models\Deal;
use App\Models\SaleDocument;
use App\Models\SaleOffer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleDeskService
{
    public function __construct(
        private InventoryService $inventory,
        private ActivityLogger $activities,
    ) {
    }

    public function createOffer(Deal $deal, array $data, ?User $user = null): SaleOffer
    {
        $unit = $this->inventory->resolveForDeal($deal);
        $deal->inventory_unit_id = $unit->id;
        $deal->setRelation('inventoryUnit', $unit);
        $list = (float) ($data['list_price'] ?? $unit?->price() ?? $deal->value ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $net = max(0, $list - $discount);

        SaleOffer::query()
            ->where('deal_id', $deal->id)
            ->whereIn('status', [SaleOfferStatus::Draft->value, SaleOfferStatus::Sent->value])
            ->update(['status' => SaleOfferStatus::Superseded->value]);

        $offer = SaleOffer::create([
            'deal_id' => $deal->id,
            'inventory_unit_id' => $deal->inventory_unit_id,
            'contact_id' => $deal->contact_id,
            'brocker_id' => $deal->brocker_id,
            'list_price' => $list,
            'discount' => $discount,
            'net_price' => $net,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'down_payment_percent' => $data['down_payment_percent'] ?? null,
            'installment_count' => $data['installment_count'] ?? null,
            'valid_until' => $data['valid_until'] ?? now()->addDays(7),
            'status' => SaleOfferStatus::Sent,
            'created_by' => $user?->id ?? auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);

        $deal->forceFill(['sale_offer_id' => $offer->id, 'value' => $net])->saveQuietly();

        $this->issueDocument($deal, SaleDocumentType::Offer, $offer, $user);

        return $offer;
    }

    public function acceptOffer(SaleOffer $offer, ?User $user = null): SaleOffer
    {
        if ($offer->status === SaleOfferStatus::Accepted) {
            return $offer;
        }

        $offer->fill([
            'status' => SaleOfferStatus::Accepted,
            'accepted_at' => now(),
        ])->save();

        $deal = $offer->deal;
        $unit = $this->inventory->resolveForDeal($deal);
        $deal->forceFill([
            'sale_offer_id' => $offer->id,
            'value' => $offer->net_price,
            'inventory_unit_id' => $unit->id,
        ])->saveQuietly();

        $this->inventory->placeHold(
            $unit,
            $deal->fresh(['contact', 'ticket']),
            \App\Enums\HoldType::Reservation,
            $offer->valid_until,
            $user,
        );

        $this->issueDocument($deal, SaleDocumentType::ReservationLetter, $offer, $user);
        $this->ensurePaymentPlan($deal, $offer);

        if ($deal->contact) {
            $this->activities->log(
                $deal->contact,
                ActivityType::Document,
                __('Offer accepted'),
                __('Net price :price.', ['price' => number_format((float) $offer->net_price)]),
                ['sale_offer_id' => $offer->id],
                $deal->ticket,
                $user,
            );
        }

        return $offer->fresh();
    }

    public function issueDocument(Deal $deal, SaleDocumentType $type, ?SaleOffer $offer = null, ?User $user = null): SaleDocument
    {
        $offer = $offer ?: $deal->activeOffer;
        $unit = $deal->inventoryUnit;

        if ($type === SaleDocumentType::SaleContract && $unit && ! in_array($unit->status, [
            InventoryStatus::Sold,
            InventoryStatus::HandedOver,
            InventoryStatus::Contracted,
        ], true)) {
            $this->inventory->markContracted($unit, $deal);
        }

        $document = SaleDocument::create([
            'deal_id' => $deal->id,
            'sale_offer_id' => $offer?->id,
            'inventory_unit_id' => $deal->inventory_unit_id,
            'contact_id' => $deal->contact_id,
            'brocker_id' => $deal->brocker_id,
            'type' => $type,
            'title' => $type->label().' #'.$deal->id,
            'body' => view('sale-documents.body', [
                'deal' => $deal->loadMissing(['contact', 'brocker.user', 'inventoryUnit.compound', 'developer']),
                'offer' => $offer,
                'unit' => $unit,
                'type' => $type,
            ])->render(),
            'issued_at' => now(),
            'issued_by' => $user?->id ?? auth()->id(),
        ]);

        if ($deal->contact) {
            $this->activities->log(
                $deal->contact,
                ActivityType::Document,
                $type->label(),
                $document->title,
                ['sale_document_id' => $document->id],
                $deal->ticket,
                $user,
            );
        }

        return $document;
    }

    public function ensurePaymentPlan(Deal $deal, ?SaleOffer $offer = null): BuyerPaymentPlan
    {
        $existing = $deal->paymentPlan;
        if ($existing) {
            return $existing;
        }

        $offer = $offer ?: $deal->activeOffer;
        $total = (float) ($offer?->net_price ?? $deal->value ?? $deal->inventoryUnit?->price() ?? 0);
        if ($total <= 0) {
            throw ValidationException::withMessages([
                'total_price' => __('Set a deal value or accept an offer before creating a payment plan.'),
            ]);
        }

        $percent = (int) ($offer?->down_payment_percent ?? ($offer?->payment_method === 'cash' ? 100 : 20));
        $down = round($total * $percent / 100, 2);
        $count = (int) ($offer?->installment_count ?? ($percent >= 100 ? 0 : 12));
        $start = now()->toDateString();

        return DB::transaction(function () use ($deal, $offer, $total, $down, $count, $start) {
            $plan = BuyerPaymentPlan::create([
                'deal_id' => $deal->id,
                'contact_id' => $deal->contact_id,
                'inventory_unit_id' => $deal->inventory_unit_id,
                'sale_offer_id' => $offer?->id,
                'total_price' => $total,
                'down_payment' => $down,
                'start_date' => $start,
                'status' => 'open',
            ]);

            $sequence = 1;
            BuyerInstallment::create([
                'buyer_payment_plan_id' => $plan->id,
                'sequence' => $sequence,
                'label' => __('Down payment'),
                'due_date' => $start,
                'amount' => $down,
                'status' => BuyerInstallmentStatus::Pending,
            ]);

            $remaining = round($total - $down, 2);
            if ($count > 0 && $remaining > 0) {
                $each = round($remaining / $count, 2);
                $allocated = 0;
                for ($i = 1; $i <= $count; $i++) {
                    $sequence++;
                    $amount = $i === $count ? round($remaining - $allocated, 2) : $each;
                    $allocated += $amount;
                    BuyerInstallment::create([
                        'buyer_payment_plan_id' => $plan->id,
                        'sequence' => $sequence,
                        'label' => __('Installment').' '.$i,
                        'due_date' => now()->addMonths($i)->toDateString(),
                        'amount' => $amount,
                        'status' => BuyerInstallmentStatus::Pending,
                    ]);
                }
            }

            return $plan->fresh('installments');
        });
    }

    public function recordReceipt(BuyerInstallment $installment, array $data, ?User $user = null): BuyerReceipt
    {
        $amount = (float) $data['amount'];
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => __('Receipt amount must be greater than zero.')]);
        }

        $receipt = BuyerReceipt::create([
            'buyer_installment_id' => $installment->id,
            'amount' => $amount,
            'received_at' => $data['received_at'] ?? now(),
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $user?->id ?? auth()->id(),
        ]);

        $installment->paid_amount = (float) $installment->paid_amount + $amount;
        $installment->refreshStatus();

        return $receipt;
    }

    public function markOverdue(): int
    {
        $rows = BuyerInstallment::query()
            ->where('due_date', '<', now()->toDateString())
            ->whereNotIn('status', [BuyerInstallmentStatus::Paid->value])
            ->get();

        foreach ($rows as $row) {
            $row->refreshStatus();
        }

        return $rows->count();
    }
}
