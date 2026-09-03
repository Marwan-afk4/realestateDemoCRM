<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Enums\CommissionPayoutStatus;
use App\Enums\DealStatuses;
use App\Enums\PipelineStage;
use App\Enums\SaleDocumentType;
use App\Models\Commission;
use App\Models\CommissionSplit;
use App\Models\Deal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DealCloser
{
    public function __construct(
        private PipelineService $pipeline,
        private ActivityLogger $activities,
        private InventoryService $inventory,
        private SaleDeskService $sales,
        private CommissionSplitService $commissionSplits,
    ) {
    }

    public function applyStatus(Deal $deal, DealStatuses $status): Deal
    {
        return DB::transaction(function () use ($deal, $status) {
            $deal->loadMissing(['uptown', 'inventoryUnit', 'ticket', 'lead', 'contact', 'brocker.user', 'brocker.teamLead', 'listerBroker.user']);
            $previous = $deal->status instanceof DealStatuses ? $deal->status : DealStatuses::tryFrom((string) $deal->status);

            if ($status === DealStatuses::Approved || $status === DealStatuses::SemiDone) {
                $unit = $this->inventory->applyDealStatus($deal->fresh(['inventoryUnit', 'uptown']), $status);
                $deal->inventory_unit_id = $unit->id;
                if (! $deal->uptown_id) {
                    $deal->uptown_id = $unit->uptown_id;
                }
                if (! $deal->value) {
                    $deal->value = $unit->price();
                }
                if (! $deal->developer_id) {
                    $deal->developer_id = $unit->developer_id;
                }
                if (! $deal->compound_id) {
                    $deal->compound_id = $unit->compound_id;
                }
            }

            if ($status === DealStatuses::Rejected && $previous !== DealStatuses::Rejected && $deal->inventory_unit_id) {
                $this->inventory->applyDealStatus($deal, $status);
            }

            $deal->status = $status;
            $deal->probability = match ($status) {
                DealStatuses::Approved => 100,
                DealStatuses::SemiDone => 90,
                DealStatuses::Rejected => 0,
                DealStatuses::Pending => $deal->probability ?: 20,
            };
            if ($status === DealStatuses::Approved && ! $deal->close_date) {
                $deal->close_date = now()->toDateString();
            }
            $deal->save();

            if ($status === DealStatuses::Approved) {
                if (! $deal->brocker_id) {
                    throw ValidationException::withMessages([
                        'brocker_id' => __('A broker is required to approve a deal and accrue commission.'),
                    ]);
                }
                $deal->load(['brocker.user', 'brocker.teamLead', 'listerBroker.user', 'inventoryUnit', 'activeOffer', 'compound']);
                $this->snapshotCommission($deal);
                if ($deal->ticket && $deal->ticket->stage !== PipelineStage::Won) {
                    $this->pipeline->changeStage($deal->ticket, PipelineStage::Won, auth()->user());
                }
                if ($deal->lead) {
                    $deal->lead->forceFill(['status' => 'done'])->saveQuietly();
                }
                $this->sales->issueDocument($deal->fresh(['contact', 'brocker.user', 'inventoryUnit.compound', 'developer', 'activeOffer']), SaleDocumentType::SaleContract, $deal->activeOffer);
                if ($deal->activeOffer || $deal->value) {
                    try {
                        $this->sales->ensurePaymentPlan($deal->fresh(['activeOffer', 'inventoryUnit', 'paymentPlan']));
                    } catch (ValidationException) {
                    }
                }
            }

            if ($status === DealStatuses::SemiDone && $deal->ticket && $deal->ticket->stage->isOpen()) {
                $this->pipeline->changeStage($deal->ticket, PipelineStage::Reserved, auth()->user());
            }

            if ($deal->contact) {
                $this->activities->log(
                    $deal->contact,
                    ActivityType::System,
                    __('Deal status updated'),
                    __('Deal #:id is now :status.', ['id' => $deal->id, 'status' => $status->label()]),
                    ['deal_id' => $deal->id, 'status' => $status->value],
                    $deal->ticket,
                );
            }

            return $deal->fresh(['uptown', 'inventoryUnit', 'commission.splits.user', 'listerBroker.user', 'ticket', 'contact']);
        });
    }

    private function snapshotCommission(Deal $deal): void
    {
        if (! $deal->brocker_id) {
            return;
        }

        $unit = $deal->inventoryUnit;
        $developerId = $deal->developer_id ?? $unit?->developer_id;
        if (! $developerId) {
            return;
        }

        $closedPrice = (float) ($deal->activeOffer?->net_price ?? $deal->value ?? $unit?->price() ?? 0);
        $percentage = (float) ($deal->brocker?->comission_percentage
            ?: $deal->compound?->commission_percentage
            ?: 0);
        $amount = round($closedPrice * $percentage / 100, 2);

        $existing = Commission::query()->where('deal_id', $deal->id)->first();
        $payoutStatus = $existing?->payout_status ?? CommissionPayoutStatus::Accrued;
        $paidAt = $existing?->paid_at;

        $listerBroker = $this->commissionSplits->resolveListerBroker($deal);
        if ($listerBroker && ! $deal->lister_broker_id) {
            $deal->forceFill(['lister_broker_id' => $listerBroker->id])->saveQuietly();
        }

        $commission = Commission::query()->updateOrCreate(
            ['deal_id' => $deal->id],
            [
                'brocker_id' => $deal->brocker_id,
                'developer_id' => $developerId,
                'uptown_id' => $deal->uptown_id ?? $unit?->uptown_id,
                'inventory_unit_id' => $deal->inventory_unit_id,
                'commission' => (int) round($amount),
                'percentage' => $percentage,
                'amount' => $amount,
                'closed_unit_price' => $closedPrice,
                'payout_status' => $payoutStatus,
                'paid_at' => $paidAt,
            ]
        );

        $commission->splits()->delete();

        foreach ($this->commissionSplits->buildForDeal($deal, $amount) as $split) {
            CommissionSplit::create([
                'commission_id' => $commission->id,
                'user_id' => $split['user_id'],
                'role' => $split['role'],
                'percentage' => $split['percentage'],
                'amount' => $split['amount'],
            ]);
        }
    }
}
