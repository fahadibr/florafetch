<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
            'photo'      => 'nullable|image|mimes:jpeg,png|max:5120|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reviews', 'public');
        }

        try {
            $this->reviewService->store(auth()->user(), $order, $product, [
                'rating'     => $data['rating'],
                'comment'    => $data['comment'] ?? null,
                'photo_path' => $photoPath,
            ]);

            return back()->with('success', 'Review submitted and pending moderation. Thank you!');
        } catch (\RuntimeException $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
