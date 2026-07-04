@extends('layouts.app')
@section('title', 'Shop Plants')
@section('content')
<div class="row">
    {{-- Sidebar Filters --}}
    <aside class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Filter Plants</h5>
                <form method="GET" action="{{ route('catalog.search') }}" id="filterForm">
                    <input type="hidden" name="q" value="{{ $query ?? '' }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        @foreach($categories as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat{{ $cat->id }}"
                                    value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat{{ $cat->id }}">{{ $cat->name }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price Range</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" name="price_min" placeholder="Min" value="{{ request('price_min') }}" min="0">
                            <span class="input-group-text">–</span>
                            <input type="number" class="form-control" name="price_max" placeholder="Max" value="{{ request('price_max') }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Growth Rate</label>
                        @foreach(['Slow','Moderate','Fast'] as $rate)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="growth_rate" id="gr{{ $rate }}"
                                    value="{{ $rate }}" {{ request('growth_rate') === $rate ? 'checked' : '' }}>
                                <label class="form-check-label" for="gr{{ $rate }}">{{ $rate }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="low_maintenance" id="lowMaint" value="1" {{ request('low_maintenance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="lowMaint">Low Maintenance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="pet_friendly" id="petFriendly" value="1" {{ request('pet_friendly') ? 'checked' : '' }}>
                            <label class="form-check-label" for="petFriendly">Pet Friendly</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-flora w-100">Apply Filters</button>
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear All</a>
                </form>
            </div>
        </div>
    </aside>

    {{-- Product Grid --}}
    <div class="col-md-9">
        @if(isset($query) && $query)
            <p class="text-muted">Results for "<strong>{{ $query }}</strong>"</p>
        @endif

        @if($products->isEmpty())
            <div class="alert alert-info">
                No plants found matching your criteria.
                @if(request()->hasAny(['category','price_min','price_max','growth_rate','low_maintenance','pet_friendly']))
                    Try removing some filters:
                    @foreach(['category','price_min','price_max','growth_rate','low_maintenance','pet_friendly'] as $f)
                        @if(request($f))
                            <a href="{{ request()->fullUrlWithoutQuery([$f]) }}" class="badge bg-secondary text-decoration-none me-1">
                                Remove {{ $f }} ×
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}" class="card-img-top" alt="{{ $product->common_name }}" style="height:200px;object-fit:cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                    <span class="fs-1">🌿</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">{{ $product->common_name }}</h6>
                                <small class="text-muted fst-italic mb-2">{{ $product->botanical_name }}</small>
                                <div class="mb-2">
                                    @if($product->is_pet_friendly)
                                        <span class="badge bg-success">Pet Friendly</span>
                                    @endif
                                    @if($product->is_low_maintenance)
                                        <span class="badge bg-info text-dark">Low Maintenance</span>
                                    @endif
                                </div>
                                <p class="fw-bold text-success mt-auto mb-2">PKR {{ number_format($product->price, 2) }}</p>
                                @if($product->isInStock())
                                    <a href="{{ route('catalog.show', $product) }}" class="btn btn-flora btn-sm">View Plant</a>
                                @else
                                    <span class="badge bg-danger w-100 py-2">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
