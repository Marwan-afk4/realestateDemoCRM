<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brocker;
use App\Models\Complaint;
use App\Models\Contract;
use App\Models\Developer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\TrainingSubscription;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'monthly');

        // Existing counts (always total)
        $userCount = User::where('role', 'user')->count();
        $brockerCount = User::where('role', 'brocker')->count();
        $unitCount = \App\Models\Uptown::count();
        $sellRequestCount = \App\Models\SellRequest::count();
        $installmentRequestCount = \App\Models\BuyAppartmentInstallment::count();

        // Monthly data for charts
        $userMonthlyCounts = $this->getMonthlyCounts(User::where('role', 'user'));
        $brockerMonthlyCounts = $this->getMonthlyCounts(User::where('role', 'brocker'));

        // Additional statistics from API with filter
        $statistics = $this->getStatistics($filter);
        $mostPlans = $this->getMostPlans();
        $comparison = $this->getComparisonData($filter);

        // Fetch API request stats filtered by the same period
        $apiLogs = \App\Models\ApiLog::selectRaw('path, method, COUNT(*) as visits')
            ->whereBetween('created_at', [$statistics['period_start'] . ' 00:00:00', $statistics['period_end'] . ' 23:59:59'])
            ->groupBy('path', 'method')
            ->orderByDesc('visits')
            ->get();

        // Monthly delivery date payouts for current year (delivery_date is YYYY-MM)
        $sellRequestMonthlyPayouts = \App\Models\SellRequest::selectRaw('CAST(SUBSTRING(delivery_date, 6, 2) AS UNSIGNED) as month, SUM(price) as total_price')
            ->whereNotNull('delivery_date')
            ->whereRaw('SUBSTRING(delivery_date, 1, 4) = ?', [(string) now()->year])
            ->groupBy('month')
            ->pluck('total_price', 'month');

        $monthlyPayouts = array_fill(1, 12, 0);
        foreach ($sellRequestMonthlyPayouts as $month => $total) {
            $monthlyPayouts[$month] = (float)$total;
        }
        $monthlyPayouts = array_values($monthlyPayouts);

        $totalPayoutCurrentYear = (float)\App\Models\SellRequest::whereNotNull('delivery_date')
            ->whereRaw('SUBSTRING(delivery_date, 1, 4) = ?', [(string) now()->year])
            ->sum('price');
        $totalPayoutAllTime = (float)\App\Models\SellRequest::whereNotNull('delivery_date')->sum('price');
        $nextUpcomingPayout = \App\Models\SellRequest::whereNotNull('delivery_date')
            ->where('delivery_date', '>=', now()->format('Y-m'))
            ->orderBy('delivery_date', 'asc')
            ->first();

        // Fetch raw delivery month/year and price data for interactive filtering
        $sellRequestsData = \App\Models\SellRequest::whereNotNull('delivery_date')
            ->whereRaw('SUBSTRING(delivery_date, 1, 4) = ?', [(string) now()->year])
            ->select('delivery_date', 'price')
            ->get();

        return view('home.welcome', compact(
            'userCount',
            'brockerCount',
            'unitCount',
            'sellRequestCount',
            'installmentRequestCount',
            'userMonthlyCounts',
            'brockerMonthlyCounts',
            'statistics',
            'mostPlans',
            'filter',
            'comparison',
            'apiLogs',
            'monthlyPayouts',
            'totalPayoutCurrentYear',
            'totalPayoutAllTime',
            'nextUpcomingPayout',
            'sellRequestsData'
        ));
    }

    private function getStatistics($filter = 'monthly')
    {
        // Date range for filtering
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        switch ($filter) {
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'previous_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'previous_year':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate = Carbon::now()->subYear()->endOfYear();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'last_90_days':
                $startDate = Carbon::now()->subDays(89)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'quarter':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                break;
            case 'previous_quarter':
                $startDate = Carbon::now()->subQuarter()->startOfQuarter();
                $endDate = Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'monthly':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Calculate total revenue with error handling
        $totalRevenue = 0;
        try {
            $totalRevenue = Deal::all()
                ->sum(function ($deal) {
                    try {
                        $unitCommissionPrice = $deal->uptown->commission_price ?? 0;
                        $stratPrice = $deal->uptown->strat_price ?? 0;
                        $developerprofit = $stratPrice - $unitCommissionPrice;
                        $commissionPercentage = $deal->brocker->comission_percentage ?? 0;
                        $brockerprofit = ($stratPrice - $developerprofit) * $commissionPercentage / 100;
                        $delerprofit = $unitCommissionPrice - $brockerprofit;
                        return max(0, $delerprofit); // Ensure non-negative
                    } catch (\Exception $e) {
                        return 0; // Skip this deal if there's an error
                    }
                });
        } catch (\Exception $e) {
            $totalRevenue = 0;
        }

        // User counts by role (current month)
        $users = User::where('role', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $brockers = User::where('role', 'brocker')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $trainers = User::where('role', 'trainer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Deal statistics with error handling
        $totalDeals = 0;
        $approvedDeals = 0;
        $rejectedDeals = 0;
        $semidoneDeals = 0;
        $pendingDeals = 0;

        try {
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
        } catch (\Exception $e) {
            // Handle case where Deal table doesn't exist
        }

        // Overall counts with error handling
        $totalBrockers = Brocker::count();
        $totalDevelopers = Developer::count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Handle optional models that might not exist
        $openComplaints = 0;
        $pendingTrainingRequests = 0;
        $pendingContracts = 0;
        $pendingPayments = 0;

        try {
            $openComplaints = Complaint::where('status', 'open')->count();
        } catch (\Exception $e) {
            // Complaint model might not exist or table might not exist
        }

        try {
            $pendingTrainingRequests = TrainingSubscription::where('status', 'pending')->count();
        } catch (\Exception $e) {
            // TrainingSubscription model might not exist
        }

        try {
            $pendingPayments = Payment::where('status', 'pending')->count();
        } catch (\Exception $e) {
            // Payment model might not exist
        }


        return [
            'total_revenue' => $totalRevenue,
            'total_brockers' => $totalBrockers,
            'total_developers' => $totalDevelopers,
            'total_admins' => $totalAdmins,
            'open_complaints' => $openComplaints,
            'pending_payments' => $pendingPayments,
            'pending_training_requests' => $pendingTrainingRequests,
            'total_deals' => $totalDeals,
            'deals_status' => [
                'approved' => $approvedDeals,
                'rejected' => $rejectedDeals,
                'semidone' => $semidoneDeals,
                'pending' => $pendingDeals,
            ],
            'user_roles_monthly' => [
                'users' => $users,
                'brockers' => $brockers,
                'trainers' => $trainers,
            ],
            'filter_period' => $filter,
            'period_start' => $startDate->format('Y-m-d'),
            'period_end' => $endDate->format('Y-m-d'),
            'period_name' => $this->getPeriodName($filter, $startDate, $endDate),
        ];
    }

    private function getMostPlans()
    {
        try {
            $payments = Payment::where('status', 'approved')->get();
            $countPlanId = $payments->countBy('plan_id');

            $data = [];
            foreach ($countPlanId as $planId => $count) {
                $plan = Plan::find($planId);
                if ($plan) {
                    $data[] = [
                        'plan_name' => $plan->name,
                        'plan_price' => $plan->price_after_discount ?? $plan->price ?? 0,
                        'total_amount' => $count * ($plan->price_after_discount ?? $plan->price ?? 0),
                        'count' => $count
                    ];
                }
            }

            // Sort by count descending
            usort($data, function($a, $b) {
                return $b['count'] - $a['count'];
            });

            return array_slice($data, 0, 5); // Top 5 plans
        } catch (\Exception $e) {
            return []; // Return empty array if there's an error
        }
    }

    private function getMonthlyCounts($query)
    {
        // Get counts grouped by month
        $counts = $query->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month');

        // Initialize all 12 months with 0
        $monthlyCounts = array_fill(1, 12, 0);

        // Replace with actual values
        foreach ($counts as $month => $count) {
            $monthlyCounts[$month] = $count;
        }

        // Return indexed array (0-based index for JS)
        return array_values($monthlyCounts);
    }

    private function getPeriodName($filter, $startDate, $endDate)
    {
        switch ($filter) {
            case 'yearly':
                return __('This Year') . ' (' . $startDate->format('Y') . ')';
            case 'previous_year':
                return __('Previous Year') . ' (' . $startDate->format('Y') . ')';
            case 'previous_month':
                return __('Previous Month') . ' (' . $startDate->format('F Y') . ')';
            case 'quarter':
                return __('This Quarter') . ' (Q' . $startDate->quarter . ' ' . $startDate->format('Y') . ')';
            case 'previous_quarter':
                return __('Previous Quarter') . ' (Q' . $startDate->quarter . ' ' . $startDate->format('Y') . ')';
            case 'last_7_days':
                return __('Last 7 Days');
            case 'last_30_days':
                return __('Last 30 Days');
            case 'last_90_days':
                return __('Last 90 Days');
            case 'monthly':
            default:
                return __('This Month') . ' (' . $startDate->format('F Y') . ')';
        }
    }

    private function getComparisonData($filter)
    {
        // Get previous period for comparison
        $previousFilter = $this->getPreviousPeriodFilter($filter);
        if (!$previousFilter) {
            return null;
        }

        try {
            $currentStats = $this->getStatistics($filter);
            $previousStats = $this->getStatistics($previousFilter);

            return [
                'deals_change' => $this->calculatePercentageChange(
                    $previousStats['total_deals'],
                    $currentStats['total_deals']
                ),
                'revenue_change' => $this->calculatePercentageChange(
                    $previousStats['total_revenue'],
                    $currentStats['total_revenue']
                ),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getPreviousPeriodFilter($filter)
    {
        switch ($filter) {
            case 'monthly':
                return 'previous_month';
            case 'yearly':
                return 'previous_year';
            case 'quarter':
                return 'previous_quarter';
            default:
                return null; // No comparison for other filters
        }
    }

    private function calculatePercentageChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
