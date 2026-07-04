<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $items = $this->cartService->getItems($user);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = $user->addresses;
        $total = $this->cartService->getTotal($user);
        $deliveryFee = config('florafetch.delivery_fee');

        return view('checkout.index', compact('items', 'addresses', 'total', 'deliveryFee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'           => 'required|exists:addresses,id',
            'delivery_date'        => 'required|date|after:today|before_or_equal:' . now()->addDays(14)->toDateString(),
            'special_instructions' => 'nullable|string|max:500',
        ]);

        try {
            $order = $this->orderService->createOrder(auth()->user(), $request->only(
                'address_id', 'delivery_date', 'special_instructions'
            ));

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! You will receive a confirmation email shortly.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
