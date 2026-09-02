<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Lead;
use App\Models\Deal;
use Illuminate\Http\Request;

class UserProfitController extends Controller
{

    public function ProfitwithLeads(Request $request){
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();
        $leads = Lead::where('brocker_id', $brocker->id)->get();

        return response()->json(['leads' => $leads], 200);
    }

    public function dealsDone(Request $request){
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();
        if (!$brocker) {
            return response()->json(['message' => 'Brocker not found'], 404);
        }
        $dealsDone = Deal::where('status', 'approved')
        ->get();

        $dealwithProfit = $dealsDone->map(function ($deal) use ($brocker) {
            $unit = $deal->uptown;
            $profit = $brocker->comission_percentage * $unit->commission_price / 100;
            return [
                'id' => $deal->id,
                'lead' => $deal->lead->lead_name,
                'unit' => $unit->id,
                'profit' => $profit
            ];
        });

        return response()->json(['dealsDone' => $dealwithProfit], 200);

    }

    public function Profit_Sales(Request $request){
    $user = $request->user();
    $brocker = Brocker::where('user_id', $user->id)->first();

    if (!$brocker) {
        return response()->json(['message' => 'Broker not found'], 404);
    }

    $deals = Deal::with(['compound', 'developer'])->get();
    $totalRevenue = $deals->sum(function ($deal) {
        $unitCommissionPrice = $deal->uptown->commission_price ?? 0; // unit com price(10) =start price(100) * com compound(10) /100
        $developerprofit = $deal->uptown->strat_price - $unitCommissionPrice; //start price =100  =90
        $brockerprofit = ($deal->uptown->strat_price - $developerprofit)*$deal->brocker->comission_percentage/100;//9
        $delerprofit =$unitCommissionPrice - $brockerprofit;
        return $delerprofit;
    });

    return response()->json([
        'profit' => $brocker->profit,
        'dealer_profit' => $totalRevenue
    ], 200);
}


}
