@extends('layouts.app')
@section('title', $product->common_name)
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Shop</a></li>
        <li class="breadcrumb-item"><a href="{{ route('catalog.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></li>
        <li class="breadcrumb-item active">{{ $product->common_name }}</li>
    </ol>
</nav>

<div class="row g-4">
    {{-- Image Gallery --}}
    <div class="col-md-5">
        @if($product->images->isNotEmpty())
            <div id="productGallery" class="carousel slide" data-bs-ride="false">
                <div class="carousel-inner rounded shadow">
                    @foreach($product->images as $i => $img)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ $img->url }}" class="d-block w-100" alt="{{ $product->common_name }}" style="height:380px;object-fit:cover;">
                        </div>
                    @endforeach
                </div>
                @if($product->images->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#productGallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productGallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" style="height:380px;">
                <span class="fs-1">🌿</span>
            </div>
        @endif
    </div>

    {{-- Product Info --}}
    <div class="col-md-7">
        <h1 class="h2">{{ $product->common_name }}</h1>
        <p class="text-muted fst-italic">{{ $product->botanical_name }}</p>
        <p class="fs-4 fw-bold text-success">PKR {{ number_format($product->price, 2) }}</p>

        <div class="mb-3">
            <span class="badge bg-secondary">{{ ucfirst($product->size) }}</span>
            <span class="badge bg-light text-dark border">{{ $product->category->name }}</span>
            @if($product->is_pet_friendly)<span class="badge bg-success">Pet Friendly</span>@endif
            @if($product->is_low_maintenance)<span class="badge bg-info text-dark">Low Maintenance</span>@endif
            @if($product->growth_rate)<span class="badge bg-warning text-dark">{{ $product->growth_rate }} Growth</span>@endif
        </div>

        @if($product->description)
            <p>{{ $product->description }}</p>
        @endif

        @if($product->isInStock())
            <p class="text-success"><i class="bi bi-check-circle-fill"></i> In Stock ({{ $product->stock_quantity }} available)</p>
            @auth
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn btn-flora btn-lg">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-flora btn-lg">Login to Add to Cart</a>
            @endauth
        @else
            <p class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Out of Stock</p>
            <button class="btn btn-secondary btn-lg" disabled aria-disabled="true">Add to Cart</button>
        @endif
    </div>
</div>

{{-- Care Guide --}}
<div class="card mt-5 shadow-sm">
    <div class="card-header bg-success text-white"><h5 class="mb-0">🌱 Care Guide</h5></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="fs-2">☀️</div>
                    <div class="fw-semibold">Sunlight</div>
                    <div class="text-muted small">{{ $product->sunlight_requirement }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="fs-2">💧</div>
                    <div class="fw-semibold">Watering</div>
                    <div class="text-muted small">{{ $product->watering_frequency }}</div>
                </div>
            </div>
            @if($product->temperature_min_c !== null)
            <div class="col-sm-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="fs-2">🌡️</div>
                    <div class="fw-semibold">Temperature</div>
                    <div class="text-muted small">{{ $product->temperature_min_c }}°C – {{ $product->temperature_max_c }}°C</div>
                </div>
            </div>
            @endif
            @if($product->soil_recommendation)
            <div class="col-sm-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="fs-2">🪴</div>
                    <div class="fw-semibold">Soil</div>
                    <div class="text-muted small">{{ $product->soil_recommendation }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Frequently Bought With --}}
@if($product->relatedProducts->isNotEmpty())
<div class="mt-5">
    <h4>Frequently Bought With</h4>
    <div class="row row-cols-2 row-cols-md-5 g-3">
        @foreach($product->relatedProducts as $related)
            <div class="col">
                <a href="{{ route('catalog.show', $related) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm text-center">
                        @if($related->primaryImage)
                            <img src="{{ $related->primaryImage->url }}" class="card-img-top" alt="{{ $related->common_name }}" style="height:100px;object-fit:cover;">
                        @endif
                        <div class="card-body p-2">
                            <small class="fw-semibold">{{ $related->common_name }}</small><br>
                            <small class="text-success">PKR {{ number_format($related->price, 2) }}</small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Reviews --}}
<div class="mt-5">
    <h4>Customer Reviews
        @if($avgRating)
            <span class="text-warning fs-5">★ {{ $avgRating }}</span>
        @endif
    </h4>

    @if($product->approvedReviews->isEmpty())
        <p class="text-muted">No reviews yet. Be the first to review this plant!</p>
    @else
        @foreach($product->approvedReviews as $review)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $review->user->name }}</strong>
                        <span class="text-warning">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>
                    @if($review->comment)<p class="mt-2 mb-1">{{ $review->comment }}</p>@endif
                    @if($review->photo_path)
                        <img src="{{ $review->photo_url }}" alt="Review photo" class="img-thumbnail mt-2" style="max-height:150px;">
                    @endif
                    @if($review->expert_advice)
                        <div class="alert alert-success mt-3 mb-0">
                            <strong>🌿 Expert Advice:</strong> {{ $review->expert_advice }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
