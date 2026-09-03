<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Complaint;
use App\Models\Developer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\TrainingSubscription;
use App\Models\Deal;
use App\Models\User;
use App\Services\Crm\DealRevenueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function __construct(private DealRevenueService $revenue)
    {
    }

    public function homepage(Request $request)
    {
        $filter = $request->input('filter', 'monthly');

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if ($filter === 'yearly') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        $totalRevenue = $this->revenue->totalRevenue($startDate, $endDate);

        $users = User::where('role', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $brockers = User::where('role', 'brocker')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $trainers = User::where('role', 'trainer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalDeals = Deal::whereBetween('created_at', [$startDate, $endDate])->count();

        $approvedDeals = Deal::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $rejectedDeals = Deal::where('status', 'rejected')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $semidoneDeals = Deal::where('status', 'semidone')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $pendingDeals = Deal::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return response()->json([
            'brocker' => Brocker::count(),
            'developer' => Developer::count(),
            'admin' => User::where('role', 'admin')->count(),
            'complaint' => Complaint::where('status', 'open')->count(),
            'triningRequest' => TrainingSubscription::where('status', 'pending')->count(),
            'deals' => $totalDeals,
            'deals_status' => [
                'approved' => $approvedDeals,
                'rejected' => $rejectedDeals,
                'semidone' => $semidoneDeals,
                'pending' => $pendingDeals,
            ],
            'user_roles' => [
                'user' => $users,
                'brocker' => $brockers,
                'trainer' => $trainers,
            ],
            'total_revenue' => $totalRevenue,
        ]);
    }

    public function getMostPlan()
    {
        $payments = Payment::where('status', 'approved')->get();
        $countPlanId = $payments->countBy('plan_id');

        $data = [];
        foreach ($countPlanId as $planId => $count) {
            $plan = Plan::find($planId);
            if ($plan) {
                $data[] = [
                    'plan_name' => $plan->name,
                    'plan_price' => $plan->price_after_discount,
                    'total_amount' => $count * $plan->price_after_discount,
                ];
            }
        }

        return response()->json(['most_plans' => $data]);
    }
}
