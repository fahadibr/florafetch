<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $user = auth()->user();
        $items = $this->cartService->getItems($user);
        $total = $this->cartService->getTotal($user);
        $deliveryFee = config('florafetch.delivery_fee');

        return view('cart.index', compact('items', 'total', 'deliveryFee'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $product = Product::findOrFail($request->product_id);

        try {
            $this->cartService->add(auth()->user(), $product);
            return back()->with('success', "{$product->common_name} added to cart.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        try {
            $this->cartService->updateQuantity(auth()->user(), $cartItem, $request->quantity);
            return back()->with('success', 'Cart updated.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function remove(CartItem $cartItem)
    {
        try {
            $this->cartService->remove(auth()->user(), $cartItem);
            return back()->with('success', 'Item removed from cart.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
