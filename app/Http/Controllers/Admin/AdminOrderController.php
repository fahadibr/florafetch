<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with('user')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(25);
        $statuses = Order::STATUS_LABELS;

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'statusHistory.updatedBy']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'estimated_delivery_date' => 'nullable|date|after:today',
            'action'                  => 'required|in:advance,refuse',
        ]);

        try {
            if ($request->action === 'refuse') {
                $this->orderService->markRefused($order, auth()->user());
                return back()->with('success', 'Order marked as Delivery Refused.');
            }

            $this->orderService->advanceStatus(
                $order,
                auth()->user(),
                $request->estimated_delivery_date
            );

            return back()->with('success', 'Order status updated to: ' . Order::STATUS_LABELS[$order->fresh()->status]);
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
