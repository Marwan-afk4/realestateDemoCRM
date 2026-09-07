<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\PipelineStage;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\PipelineTicket;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    use RespondsJson;

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-crm-reports') || $request->user()->can('view-pipeline'), 403);

        $visibility = app(SalesVisibility::class);
        $tickets = $visibility->scopeTickets(PipelineTicket::query(), $request->user());
        $deals = $visibility->scopeDeals(Deal::query(), $request->user());

        $bySource = (clone $tickets)
            ->join('contacts', 'contacts.id', '=', 'pipeline_tickets.contact_id')
            ->select('contacts.source', DB::raw('count(*) as total'))
            ->groupBy('contacts.source')
            ->pluck('total', 'source');

        $byStage = (clone $tickets)
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $total = max(1, (clone $tickets)->count());
        $won = (clone $tickets)->where('stage', PipelineStage::Won)->count();
        $lost = (clone $tickets)->where('stage', PipelineStage::Lost)->count();

        $closed = (clone $tickets)->whereIn('stage', [PipelineStage::Won, PipelineStage::Lost])
            ->whereNotNull('stage_changed_at')
            ->get(['created_at', 'stage_changed_at']);

        $avgDays = $closed->avg(fn ($row) => $row->created_at->diffInDays($row->stage_changed_at));

        $firstContactHours = DB::table('crm_activities')
            ->join('pipeline_tickets', 'pipeline_tickets.contact_id', '=', 'crm_activities.contact_id')
            ->whereIn('crm_activities.type', ['call', 'whatsapp', 'sms', 'email', 'meeting', 'site_visit'])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, pipeline_tickets.created_at, crm_activities.created_at)) as hours')
            ->value('hours');

        $brokerStats = $visibility->scopeTickets(PipelineTicket::query()->with('brocker.user'), $request->user())
            ->whereNotNull('brocker_id')
            ->get()
            ->groupBy('brocker_id')
            ->map(function ($rows, $brokerId) {
                return [
                    'brocker_id' => $brokerId,
                    'assigned' => $rows->count(),
                    'won' => $rows->where('stage', PipelineStage::Won)->count(),
                    'lost' => $rows->where('stage', PipelineStage::Lost)->count(),
                    'broker_name' => $rows->first()->brocker?->user?->full_name,
                ];
            })
            ->values();

        $forecast = (clone $deals)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->get()
            ->sum(fn (Deal $deal) => ((float) ($deal->value ?? 0)) * ((int) ($deal->probability ?? 0)) / 100);

        $closedCommission = (clone $deals)
            ->where('status', 'approved')
            ->with('commission')
            ->get()
            ->sum(fn (Deal $deal) => (float) ($deal->commission?->amount ?? 0));

        $lostReasons = (clone $tickets)
            ->where('stage', PipelineStage::Lost)
            ->whereNotNull('lost_reason')
            ->select('lost_reason', DB::raw('count(*) as total'))
            ->groupBy('lost_reason')
            ->pluck('total', 'lost_reason');

        return $this->ok([
            'by_source' => $bySource,
            'by_stage' => $byStage,
            'conversion' => round($won / $total * 100, 1),
            'won' => $won,
            'lost' => $lost,
            'total' => (clone $tickets)->count(),
            'avg_days' => round($avgDays ?? 0, 1),
            'first_contact_hours' => $firstContactHours !== null ? round($firstContactHours, 1) : null,
            'broker_stats' => $brokerStats,
            'forecast' => $forecast,
            'closed_commission' => $closedCommission,
            'lost_reasons' => $lostReasons,
        ]);
    }
}
