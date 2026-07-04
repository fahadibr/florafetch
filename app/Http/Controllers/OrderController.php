<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product.primaryImage', 'statusHistory.updatedBy']);

        // Determine which products in this order can still be reviewed
        $reviewableProductIds = [];
        if ($order->status === 'delivered') {
            foreach ($order->items as $item) {
                if ($item->product_id && $this->reviewService->canReview(auth()->user(), $order, $item->product)) {
                    $reviewableProductIds[] = $item->product_id;
                }
            }
        }

        return view('orders.show', compact('order', 'reviewableProductIds'));
    }
}
