@extends('layouts.admin')
@section('title', 'Review Moderation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Review Moderation Queue</h3>
    <span class="badge bg-warning text-dark fs-6">{{ $reviews->total() }} pending</span>
</div>

@forelse($reviews as $review)
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $review->user->name }}</strong> reviewed
                <a href="{{ route('catalog.show', $review->product) }}" target="_blank">{{ $review->product->common_name }}</a>
            </div>
            <small class="text-muted">{{ $review->created_at->format('d M Y, H:i') }}</small>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <span class="text-warning fs-5">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
            </div>
            @if($review->comment)
                <p class="mb-2">{{ $review->comment }}</p>
            @endif
            @if($review->photo_path)
                <img src="{{ $review->photo_url }}" alt="Review photo" class="img-thumbnail mb-3" style="max-height:150px;">
            @endif

            <div class="d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm">✓ Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger btn-sm">✗ Reject</button>
                </form>
            </div>

            {{-- Expert Advice Form --}}
            <div class="mt-3">
                <button class="btn btn-outline-success btn-sm" type="button"
                        data-bs-toggle="collapse" data-bs-target="#advice{{ $review->id }}"
                        aria-expanded="false" aria-controls="advice{{ $review->id }}">
                    💬 Post Expert Advice
                </button>
                <div class="collapse mt-2" id="advice{{ $review->id }}">
                    <form method="POST" action="{{ route('admin.reviews.advice', $review) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="expert_advice_{{ $review->id }}" class="form-label">Expert Advice</label>
                            <textarea class="form-control" id="expert_advice_{{ $review->id }}" name="expert_advice"
                                      rows="3" maxlength="2000" required
                                      placeholder="Share your expert plant care advice…">{{ $review->expert_advice }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Post Advice & Notify Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>No pending reviews. The moderation queue is clear!
    </div>
@endforelse

<div class="mt-3">{{ $reviews->links() }}</div>
@endsection
