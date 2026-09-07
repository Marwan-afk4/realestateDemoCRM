<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Http\Request;

class UserProfitController extends Controller
{
    public function ProfitwithLeads(Request $request)
    {
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();
        if (! $brocker) {
            return response()->json(['message' => 'Brocker not found'], 404);
        }
        $leads = Lead::where('brocker_id', $brocker->id)->get();

        return response()->json(['leads' => $leads], 200);
    }

    public function dealsDone(Request $request)
    {
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();
        if (! $brocker) {
            return response()->json(['message' => 'Brocker not found'], 404);
        }

        $dealwithProfit = Deal::query()
            ->with(['uptown', 'lead'])
            ->where('status', 'approved')
            ->get()
            ->filter(fn (Deal $deal) => $deal->uptown)
            ->map(function (Deal $deal) use ($brocker) {
                $unit = $deal->uptown;
                $profit = ($brocker->comission_percentage ?? 0) * ($unit->commission_price ?? 0) / 100;

                return [
                    'id' => $deal->id,
                    'lead' => $deal->lead?->lead_name ?? $deal->fullname,
                    'unit' => $unit->id,
                    'profit' => $profit,
                ];
            })
            ->values();

        return response()->json(['dealsDone' => $dealwithProfit], 200);
    }

    public function Profit_Sales(Request $request)
    {
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();

        if (! $brocker) {
            return response()->json(['message' => 'Broker not found'], 404);
        }

        $deals = Deal::with(['uptown', 'brocker'])->get();
        $totalRevenue = $deals->sum(function (Deal $deal) use ($brocker) {
            $uptown = $deal->uptown;
            if (! $uptown) {
                return 0;
            }

            $unitCommissionPrice = $uptown->commission_price ?? 0;
            $startPrice = $uptown->strat_price ?? 0;
            $commissionPct = $deal->brocker?->comission_percentage
                ?? $brocker->comission_percentage
                ?? 0;
            $developerprofit = $startPrice - $unitCommissionPrice;
            $brockerprofit = ($startPrice - $developerprofit) * $commissionPct / 100;

            return $unitCommissionPrice - $brockerprofit;
        });

        return response()->json([
            'profit' => $brocker->profit,
            'dealer_profit' => $totalRevenue,
        ], 200);
    }
}
