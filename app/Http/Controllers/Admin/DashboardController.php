<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'total_products' => Product::count(),
            'low_stock' => Product::where('stock', '<', 10)->count(),
            'total_users' => User::where('is_admin', false)->count(),
            'avg_order_value' => Order::where('payment_status', 'paid')->avg('total') ?? 0,
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();

        // Chart data for orders by status
        $order_status = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Chart data for revenue by month
        $revenue_raw = Order::where('payment_status', 'paid')
            ->selectRaw("DATE_FORMAT(created_at, '%m') as month, SUM(total) as revenue")
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Map month numbers to month names
        $months = ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                   '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
        $revenue_data = [];
        foreach ($revenue_raw as $month => $revenue) {
            $revenue_data[$months[$month] ?? $month] = $revenue;
        }

        return view('admin.dashboard', compact('stats', 'recent_orders', 'order_status', 'revenue_data'));
    }
}
