<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = [
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders' => Order::count(),
            'avg_order_value' => Order::where('payment_status', 'paid')->avg('total') ?? 0,
            'total_items_sold' => Order::whereHas('items')->count(),
            'conversion_rate' => Order::count() > 0 ? round((Order::where('payment_status', 'paid')->count() / Order::count()) * 100, 2) : 0,
            'fulfillment_rate' => Order::count() > 0 ? round((Order::where('status', 'delivered')->count() / Order::count()) * 100, 2) : 0,
        ];

        // Order status breakdown
        $order_status = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Payment status breakdown
        $payment_status = Order::selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        // Revenue by month
        $revenue_raw = Order::where('payment_status', 'paid')
            ->selectRaw("strftime('%m', created_at) as month, SUM(total) as revenue")
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Map month numbers to month names
        $months = ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun',
                   '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
        $revenue_by_month = [];
        foreach ($revenue_raw as $month => $revenue) {
            $revenue_by_month[$months[$month] ?? $month] = $revenue;
        }

        // Top products
        $top_products = Product::selectRaw('products.id, products.name, COUNT(order_items.id) as sold')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('reports', 'order_status', 'payment_status', 'revenue_by_month', 'top_products'));
    }
}
