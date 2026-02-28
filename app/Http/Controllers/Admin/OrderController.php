<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);
        return redirect()->back()->with('success', 'Order status updated');
    }

    public function markAsShipped(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string',
        ]);

        $order->markAsShipped($validated['tracking_number'] ?? null);
        return redirect()->back()->with('success', 'Order marked as shipped');
    }

    public function markAsDelivered(Order $order)
    {
        $order->markAsDelivered();
        return redirect()->back()->with('success', 'Order marked as delivered');
    }
}
