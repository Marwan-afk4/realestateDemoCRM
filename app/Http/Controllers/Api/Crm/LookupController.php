<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\ActivityType;
use App\Enums\AfterSalesTicketStatus;
use App\Enums\AfterSalesTicketType;
use App\Enums\CommissionPayoutStatus;
use App\Enums\ContactSource;
use App\Enums\CrmTaskType;
use App\Enums\HoldType;
use App\Enums\InventoryStatus;
use App\Enums\LostReason;
use App\Enums\MessageChannel;
use App\Enums\PipelineStage;
use App\Enums\PipelineTicketType;
use App\Enums\SaleDocumentType;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\UptownType;
use App\Models\SaleOffer;

class LookupController extends Controller
{
    use RespondsJson;

    public function index()
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';

        return $this->ok([
            'sources' => $this->enumOptions(ContactSource::labels()),
            'stages' => $this->enumOptions(PipelineStage::labels()),
            'ticket_types' => $this->enumOptions(PipelineTicketType::labels()),
            'lost_reasons' => $this->enumOptions(LostReason::labels()),
            'activity_types' => $this->enumOptions(ActivityType::labels()),
            'task_types' => $this->enumOptions(CrmTaskType::labels()),
            'channels' => $this->enumOptions(MessageChannel::labels()),
            'inventory_statuses' => $this->enumOptions(InventoryStatus::labels()),
            'hold_types' => $this->enumOptions(HoldType::labels()),
            'document_types' => $this->enumOptions(SaleDocumentType::labels()),
            'payout_statuses' => $this->enumOptions(CommissionPayoutStatus::labels()),
            'after_sales_types' => $this->enumOptions(AfterSalesTicketType::labels()),
            'after_sales_statuses' => $this->enumOptions(AfterSalesTicketStatus::labels()),
            'payment_methods' => $this->enumOptions(SaleOffer::paymentMethods()),
            'intents' => $this->enumOptions([
                'buy' => __('Buy'),
                'rent' => __('Rent'),
                'sell' => __('Sell'),
                'mortgage' => __('Mortgage'),
            ]),
            'uptown_types' => UptownType::orderBy($nameColumn)->get(['id', 'name_en', 'name_ar']),
            'developers' => Developer::orderBy($nameColumn)->get(['id', 'name_en', 'name_ar']),
            'compounds' => Compound::orderBy('compound_name')->get(['id', 'compound_name', 'developer_id']),
            'brokers' => Brocker::with('user')->get()->map(fn (Brocker $broker) => [
                'id' => $broker->id,
                'user_id' => $broker->user_id,
                'name' => $broker->user?->full_name ?? '#'.$broker->id,
            ]),
        ]);
    }
}
