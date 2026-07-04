<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ReviewService
{
    public function canReview(User $user, Order $order, Product $product): bool
    {
        if ($order->user_id !== $user->id) {
            return false;
        }

        if ($order->status !== 'delivered') {
            return false;
        }

        $inOrder = $order->items()->where('product_id', $product->id)->exists();
        if (!$inOrder) {
            return false;
        }

        $alreadyReviewed = Review::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->exists();

        return !$alreadyReviewed;
    }

    public function store(User $user, Order $order, Product $product, array $data): Review
    {
        if (!$this->canReview($user, $order, $product)) {
            throw new \RuntimeException('You cannot submit a review for this item.');
        }

        return Review::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
            'photo_path' => $data['photo_path'] ?? null,
            'status'     => 'pending',
        ]);
    }

    public function getAverageRating(Product $product): ?float
    {
        $avg = $product->approvedReviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}
