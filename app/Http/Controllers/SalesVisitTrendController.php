<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesVisitTrendController extends Controller
{
    /**
     * Get visit trend data based on period
     * Support: daily, weekly, monthly, yearly, custom
     */
    public function getVisitTrend(Request $request)
    {
        try {
            $period = $request->input('period', 'monthly');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $userId = $request->input('user_id'); // <─ NEW

            // Custom range
            if ($startDate && $endDate) {
                return $this->getCustomRangeTrend(
                    Carbon::parse($startDate),
                    Carbon::parse($endDate),
                    $userId
                );
            }

            $limit = $this->getLimitByPeriod($period);

            switch ($period) {
                case 'daily':
                    return $this->getDailyTrend($limit, $userId);
                case 'weekly':
                    return $this->getWeeklyTrend($limit, $userId);
                case 'monthly':
                    return $this->getMonthlyTrend($limit, $userId);
                case 'yearly':
                    return $this->getYearlyTrend($limit, $userId);
                default:
                    return $this->getMonthlyTrend($limit, $userId);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Error: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply user filter (sales_id)
     */
    private function applyUserFilter($query, $userId)
    {
        if (!empty($userId) && $userId !== "all") {
            $query->where("sales_id", $userId);
        }
        return $query;
        
    }

    /**
     * Custom Range — Auto detect grouping
     */
    private function getCustomRangeTrend($startDate, $endDate, $userId = null)
    {
        $daysDiff = $startDate->diffInDays($endDate);

        if ($daysDiff <= 31) {
            return $this->getCustomDailyTrend($startDate, $endDate, $userId);
        } elseif ($daysDiff <= 90) {
            return $this->getCustomWeeklyTrend($startDate, $endDate, $userId);
        } elseif ($daysDiff <= 730) {
            return $this->getCustomMonthlyTrend($startDate, $endDate, $userId);
        } else {
            return $this->getCustomYearlyTrend($startDate, $endDate, $userId);
        }
    }

    /**
     * Custom daily
     */
    private function getCustomDailyTrend($start, $end, $userId)
    {
        $query = DB::table('sales_visits')
            ->select(
                DB::raw('DATE(visit_date) as date'),
                DB::raw('COUNT(*) as visit_count')
            );
        
        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('DATE(visit_date)'))
            ->orderBy('date')
            ->get();

        return $this->buildDailyResponse($visits, $start, $end, 'custom-daily');
    }

    /**
     * Custom weekly
     */
    private function getCustomWeeklyTrend($start, $end, $userId)
    {
        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('EXTRACT(WEEK FROM visit_date) as week'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'), DB::raw('EXTRACT(WEEK FROM visit_date)'))
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        return $this->buildWeeklyResponse($visits, $start, $end, 'custom-weekly');
    }

    /**
     * Custom monthly
     */
    private function getCustomMonthlyTrend($start, $end, $userId)
    {
        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('EXTRACT(MONTH FROM visit_date) as month'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'), DB::raw('EXTRACT(MONTH FROM visit_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $this->buildMonthlyResponse($visits, $start, $end, 'custom-monthly');
    }

    /**
     * Custom yearly
     */
    private function getCustomYearlyTrend($start, $end, $userId)
    {
        $startYear = $start->year;
        $endYear = $end->year;

        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereRaw('EXTRACT(YEAR FROM visit_date) >= ?', [$startYear])
            ->whereRaw('EXTRACT(YEAR FROM visit_date) <= ?', [$endYear])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'))
            ->orderBy('year')
            ->get();

        return $this->buildYearlyResponse($visits, $startYear, $endYear, 'custom-yearly');
    }

    /**
     * DAILY — last 30 days
     */
    private function getDailyTrend($limit, $userId)
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays($limit);

        $query = DB::table('sales_visits')
            ->select(
                DB::raw('DATE(visit_date) as date'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('DATE(visit_date)'))
            ->orderBy('date')
            ->get();

        return $this->buildDailyResponse($visits, $start, $end, 'daily');
    }

    /**
     * WEEKLY — last 12 weeks
     */
    private function getWeeklyTrend($limit, $userId)
    {
        $end = Carbon::now();
        $start = Carbon::now()->subWeeks($limit);

        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('EXTRACT(WEEK FROM visit_date) as week'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'), DB::raw('EXTRACT(WEEK FROM visit_date)'))
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        return $this->buildWeeklyResponse($visits, $start, $end, 'weekly');
    }

    /**
     * MONTHLY — last 12 months
     */
    private function getMonthlyTrend($limit, $userId)
    {
        $end = Carbon::now();
        $start = Carbon::now()->subMonths($limit);

        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('EXTRACT(MONTH FROM visit_date) as month'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereBetween('visit_date', [$start, $end])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'), DB::raw('EXTRACT(MONTH FROM visit_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $this->buildMonthlyResponse($visits, $start, $end, 'monthly');
    }

    /**
     * YEARLY — last 5 years
     */
    private function getYearlyTrend($limit, $userId)
    {
        $endYear = Carbon::now()->year;
        $startYear = $endYear - $limit + 1;

        $query = DB::table('sales_visits')
            ->select(
                DB::raw('EXTRACT(YEAR FROM visit_date) as year'),
                DB::raw('COUNT(*) as visit_count')
            );

        $query = $this->applyUserFilter($query, $userId);

        $visits = $query
            ->whereRaw('EXTRACT(YEAR FROM visit_date) >= ?', [$startYear])
            ->groupBy(DB::raw('EXTRACT(YEAR FROM visit_date)'))
            ->orderBy('year')
            ->get();

        return $this->buildYearlyResponse($visits, $startYear, $endYear, 'yearly');
    }

    /**
     * Reusable response builders
     */
    private function buildDailyResponse($visits, $start, $end, $period)
    {
        $all = [];
        $current = $start->copy();

        while ($current <= $end) {
            $d = $current->format('Y-m-d');
            $found = $visits->firstWhere('date', $d);

            $all[] = [
                'label' => $current->format('d M'),
                'count' => $found->visit_count ?? 0
            ];

            $current->addDay();
        }

        $cumulative = array_sum(array_column($all, 'count'));

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'labels' => array_column($all, 'label'),
                'visits' => array_column($all, 'count')
            ],
            'stats' => [
                'total_visits' => $cumulative,
                'average_per_day' => count($all) ? round($cumulative / count($all), 1) : 0,
                'period_label' => $start->format('d M Y') . " - " . $end->format('d M Y')
            ]
        ]);
    }

    private function buildWeeklyResponse($visits, $start, $end, $period)
    {
        $all = [];
        $current = $start->copy()->startOfWeek();

        while ($current <= $end) {
            $yr = $current->year;
            $wk = $current->week;

            $found = $visits
                ->where('year', $yr)
                ->where('week', $wk)
                ->first();

            $all[] = [
                'label' => "W$wk",
                'count' => $found->visit_count ?? 0
            ];

            $current->addWeek();
        }

        $total = array_sum(array_column($all, 'count'));

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'labels' => array_column($all, 'label'),
                'visits' => array_column($all, 'count')
            ],
            'stats' => [
                'total_visits' => $total,
                'average_per_week' => count($all) ? round($total / count($all), 1) : 0,
                'period_label' => $start->format('d M Y') . " - " . $end->format('d M Y')
            ]
        ]);
    }

    private function buildMonthlyResponse($visits, $start, $end, $period)
    {
        $all = [];
        $current = $start->copy()->startOfMonth();

        while ($current <= $end) {
            $yr = $current->year;
            $mn = $current->month;

            $found = $visits
                ->where('year', $yr)
                ->where('month', $mn)
                ->first();

            $all[] = [
                'label' => $current->format('M Y'),
                'count' => $found->visit_count ?? 0
            ];

            $current->addMonth();
        }

        $total = array_sum(array_column($all, 'count'));

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'labels' => array_column($all, 'label'),
                'visits' => array_column($all, 'count')
            ],
            'stats' => [
                'total_visits' => $total,
                'average_per_month' => count($all) ? round($total / count($all), 1) : 0,
                'period_label' => $start->format('M Y') . " - " . $end->format('M Y')
            ]
        ]);
    }

    private function buildYearlyResponse($visits, $startYear, $endYear, $period)
    {
        $all = [];
        for ($yr = $startYear; $yr <= $endYear; $yr++) {
            $found = $visits->firstWhere('year', $yr);

            $all[] = [
                'label' => "$yr",
                'count' => $found->visit_count ?? 0
            ];
        }

        $total = array_sum(array_column($all, 'count'));

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'labels' => array_column($all, 'label'),
                'visits' => array_column($all, 'count')
            ],
            'stats' => [
                'total_visits' => $total,
                'average_per_year' => count($all) ? round($total / count($all), 1) : 0,
                'period_label' => "$startYear - $endYear"
            ]
        ]);
    }

    /**
     * Period limits
     */
    private function getLimitByPeriod($period)
    {
        return match ($period) {
            'daily' => 30,
            'weekly' => 12,
            'monthly' => 12,
            'yearly' => 5,
            default => 12
        };
    }
}
