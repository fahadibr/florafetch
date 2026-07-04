<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $metrics = Cache::remember('admin_dashboard_metrics', 300, function () {
            $totalOrders   = Order::count();
            $totalRevenue  = Order::whereIn('status', ['delivered'])->sum('total_amount');
            $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return compact('totalOrders', 'totalRevenue', 'ordersByStatus');
        });

        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard.index', compact('metrics', 'recentOrders'));
    }
}
