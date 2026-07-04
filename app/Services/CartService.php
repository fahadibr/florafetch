<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    public function getItems(User $user): Collection
    {
        return $user->cartItems()->with('product.primaryImage')->get();
    }

    public function getTotal(User $user): float
    {
        return $user->cartItems()->with('product')->get()
            ->sum(fn($item) => $item->quantity * $item->product->price);
    }

    public function add(User $user, Product $product): CartItem
    {
        if ($product->stock_quantity <= 0) {
            throw new \RuntimeException('This item is out of stock.');
        }

        $existing = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + 1;
            if ($newQty > $product->stock_quantity) {
                throw new \RuntimeException("Only {$product->stock_quantity} available.");
            }
            $existing->update(['quantity' => $newQty]);
            return $existing->fresh();
        }

        return CartItem::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);
    }

    public function updateQuantity(User $user, CartItem $item, int $quantity): void
    {
        if ($item->user_id !== $user->id) {
            throw new \RuntimeException('Unauthorized.');
        }

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        if ($quantity > $item->product->stock_quantity) {
            throw new \RuntimeException("Only {$item->product->stock_quantity} available.");
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(User $user, CartItem $item): void
    {
        if ($item->user_id !== $user->id) {
            throw new \RuntimeException('Unauthorized.');
        }
        $item->delete();
    }

    public function clear(User $user): void
    {
        $user->cartItems()->delete();
    }

    public function count(User $user): int
    {
        return $user->cartItems()->sum('quantity');
    }
}
