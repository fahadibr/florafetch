<?php

namespace App\Http\Controllers\Admin;

use App\Events\ExpertAdvicePosted;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::pending()
            ->with(['user', 'product'])
            ->orderBy('created_at')
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Review rejected.');
    }

    public function postAdvice(Request $request, Review $review)
    {
        $request->validate(['expert_advice' => 'required|string|max:2000']);

        $review->update([
            'expert_advice'            => $request->expert_advice,
            'expert_advice_posted_at'  => now(),
        ]);

        event(new ExpertAdvicePosted($review->fresh()->load('user', 'product')));

        return back()->with('success', 'Expert advice posted and customer notified.');
    }
}
