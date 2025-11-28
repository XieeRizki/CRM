<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesPerformanceController extends Controller
{
    public function getSalesPerformance(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            // Check if user is authenticated
            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.'
                ], 401);
            }
            
            $requestedUserId = $request->input('user_id');
            
            // Determine which user(s) to query
            $query = DB::table('sales_visits')
                ->join('users', DB::raw('CAST(sales_visits.sales_id AS INTEGER)'), '=', 'users.user_id')
                ->where('users.role_id', 12);  // Filter only Sales role
            
            // If sales (role_id = 12), only show their own data
            if ($currentUser->role_id == 12) {
                $query->where('users.user_id', $currentUser->user_id);
            } 
            // If superadmin (role_id = 1) and specific user requested
            elseif ($currentUser->role_id == 1 && $requestedUserId) {
                $query->where('users.user_id', $requestedUserId);
            }
            // Else: superadmin viewing all sales (no additional filter)

            $salesPerformance = $query
                ->select(
                    'users.user_id',
                    'users.username as sales_name',
                    DB::raw('COUNT(DISTINCT sales_visits.company_id) as company_visited')
                )
                ->groupBy('users.user_id', 'users.username')
                ->orderByRaw('COUNT(DISTINCT sales_visits.company_id) DESC')
                ->get();

            if ($salesPerformance->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'labels' => [],
                        'datasets' => [
                            [
                                'label' => 'Company Visited',
                                'data' => [],
                                'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                                'borderColor' => 'rgb(59, 130, 246)',
                                'borderWidth' => 1
                            ],
                            [
                                'label' => 'Deal',
                                'data' => [],
                                'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                'borderColor' => 'rgb(16, 185, 129)',
                                'borderWidth' => 1
                            ],
                            [
                                'label' => 'Fails',
                                'data' => [],
                                'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                                'borderColor' => 'rgb(239, 68, 68)',
                                'borderWidth' => 1
                            ]
                        ]
                    ],
                    'stats' => [
                        'total_company_visited' => 0,
                        'total_deal' => 0,
                        'total_fails' => 0,
                        'active_sales' => 0,
                        'top_performer_name' => '-',
                        'top_performer_visits' => 0
                    ],
                    'details' => [],
                    'message' => 'No sales data available'
                ]);
            }

            // Query REAL data Deal & Fails from transaksi table
            $dealsData = [];
            $failsData = [];
            
            foreach ($salesPerformance as $sales) {
                // Count Deals (status = 'Deals')
                $dealCount = DB::table('transaksi')
                    ->whereRaw('CAST(transaksi.sales_id AS INTEGER) = ?', [$sales->user_id])
                    ->where('status', 'Deals')
                    ->distinct('company_id')
                    ->count('company_id');
                
                // Count Fails (status = 'Fails')
                $failCount = DB::table('transaksi')
                    ->whereRaw('CAST(transaksi.sales_id AS INTEGER) = ?', [$sales->user_id])
                    ->where('status', 'Fails')
                    ->distinct('company_id')
                    ->count('company_id');
                
                $dealsData[] = $dealCount;
                $failsData[] = $failCount;
            }

            // Calculate statistics
            $totalCompanyVisited = $salesPerformance->sum('company_visited');
            $totalDeal = array_sum($dealsData);
            $totalFails = array_sum($failsData);
            $totalActiveSales = $salesPerformance->count();
            $topPerformer = $salesPerformance->first();

            // Format data for Chart.js - 3 Bars per Sales
            $chartData = [
                'labels' => $salesPerformance->pluck('sales_name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Company Visited',
                        'data' => $salesPerformance->pluck('company_visited')->toArray(),
                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Deal',
                        'data' => $dealsData,
                        'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                        'borderColor' => 'rgb(16, 185, 129)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Fails',
                        'data' => $failsData,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgb(239, 68, 68)',
                        'borderWidth' => 1
                    ]
                ]
            ];

            // Summary stats
            $stats = [
                'total_company_visited' => $totalCompanyVisited,
                'total_deal' => $totalDeal,
                'total_fails' => $totalFails,
                'active_sales' => $totalActiveSales,
                'top_performer_name' => $topPerformer ? $topPerformer->sales_name : '-',
                'top_performer_visits' => $topPerformer ? $topPerformer->company_visited : 0,
                'success_rate' => $totalCompanyVisited > 0 ? round(($totalDeal / $totalCompanyVisited) * 100, 1) : 0
            ];

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'stats' => $stats,
                'details' => $salesPerformance,
                'current_user_role' => $currentUser->role_id,
                'note' => 'Deal and Fails data from transaksi table'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function getSalesDetail($userId)
    {
        try {
            $currentUser = Auth::user();
            
            // Check authentication
            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.'
                ], 401);
            }
            
            // Security check: Sales can only see their own data
            if ($currentUser->role_id == 12 && $currentUser->user_id != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You can only view your own data'
                ], 403);
            }

            // Get sales info with role check
            $sales = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.role_id')
                ->where('users.user_id', $userId)
                ->where('users.role_id', 12)
                ->select('users.user_id', 'users.username', 'roles.role_name')
                ->first();
            
            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales not found or user is not a sales role'
                ], 404);
            }

            // Get companies visited with REAL status from transaksi
            $companyVisited = DB::table('sales_visits')
                ->join('company', 'sales_visits.company_id', '=', 'company.company_id')
                ->leftJoin('transaksi', function($join) use ($userId) {
                    $join->on('sales_visits.company_id', '=', 'transaksi.company_id')
                         ->whereRaw('CAST(transaksi.sales_id AS INTEGER) = ?', [$userId]);
                })
                ->whereRaw('CAST(sales_visits.sales_id AS INTEGER) = ?', [$userId])
                ->select(
                    'company.company_id',
                    'company.company_name',
                    'company.tier',
                    'company.status as company_status',
                    DB::raw('COUNT(DISTINCT sales_visits.id) as visit_count'),
                    DB::raw('MAX(sales_visits.visit_date) as last_visit'),
                    DB::raw('MIN(sales_visits.visit_date) as first_visit'),
                    DB::raw('MAX(transaksi.status) as deal_status')
                )
                ->groupBy('company.company_id', 'company.company_name', 'company.tier', 'company.status')
                ->orderByRaw('COUNT(DISTINCT sales_visits.id) DESC')
                ->get();

            // Map with REAL status from transaksi
            $companyWithStatus = $companyVisited->map(function($company) {
                $status = $company->deal_status ?? 'Pending';
                
                if ($status === 'Deals') {
                    $statusColor = 'green';
                } elseif ($status === 'Fails') {
                    $statusColor = 'red';
                } else {
                    $statusColor = 'gray';
                }
                
                return [
                    'company_id' => $company->company_id,
                    'company_name' => $company->company_name,
                    'tier' => $company->tier ?? '-',
                    'visit_count' => $company->visit_count,
                    'last_visit' => $company->last_visit,
                    'first_visit' => $company->first_visit,
                    'status' => $status,
                    'status_color' => $statusColor
                ];
            });

            $dealCount = $companyWithStatus->where('status', 'Deals')->count();
            $failsCount = $companyWithStatus->where('status', 'Fails')->count();
            $pendingCount = $companyWithStatus->where('status', 'Pending')->count();

            return response()->json([
                'success' => true,
                'sales' => [
                    'id' => $sales->user_id,
                    'name' => $sales->username,
                    'role' => $sales->role_name
                ],
                'company_visited' => [
                    'total' => $companyVisited->count(),
                    'deal' => $dealCount,
                    'fails' => $failsCount,
                    'pending' => $pendingCount,
                    'total_visits' => $companyVisited->sum('visit_count'),
                    'details' => $companyWithStatus
                ],

            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function getSalesList()
    {
        try {
            $currentUser = Auth::user();
            
            // Only superadmin can see all sales list
            if ($currentUser->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only superadmin can access this endpoint'
                ], 403);
            }

            $salesList = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.role_id')
                ->leftJoin('sales_visits', DB::raw('users.user_id'), '=', DB::raw('CAST(sales_visits.sales_id AS INTEGER)'))
                ->where('users.role_id', 12)
                ->where('users.is_active', true)
                ->select(
                    'users.user_id',
                    'users.username',
                    'users.email',
                    'roles.role_name',
                    DB::raw('COUNT(DISTINCT sales_visits.company_id) as total_companies')
                )
                ->groupBy('users.user_id', 'users.username', 'users.email', 'roles.role_name')
                ->orderBy('users.username')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $salesList,
                'count' => $salesList->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}