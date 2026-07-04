@extends('layouts.app')
@section('title', 'Your Cart')
@section('content')
<h2 class="mb-4">🛒 Your Cart</h2>

@if($items->isEmpty())
    <div class="alert alert-info">
        Your cart is empty. <a href="{{ route('catalog.index') }}">Browse plants</a>
    </div>
@else
    <div class="row">
        <div class="col-md-8">
            @foreach($items as $item)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        @if($item->product->primaryImage)
                            <img src="{{ $item->product->primaryImage->url }}" alt="{{ $item->product->common_name }}" style="width:80px;height:80px;object-fit:cover;" class="rounded">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:80px;height:80px;"><span class="fs-3">🌿</span></div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $item->product->common_name }}</h6>
                            <small class="text-muted">PKR {{ number_format($item->product->price, 2) }} each</small>
                        </div>
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="d-flex align-items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="{{ $item->product->stock_quantity }}" class="form-control form-control-sm" style="width:70px;" aria-label="Quantity">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                        </form>
                        <strong class="text-success">PKR {{ number_format($item->subtotal, 2) }}</strong>
                        <form method="POST" action="{{ route('cart.remove', $item) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Remove item"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Green Total</span>
                        <strong>PKR {{ number_format($total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Delivery Fee</span>
                        <strong>PKR {{ number_format($deliveryFee, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total (COD)</span>
                        <strong class="text-success fs-5">PKR {{ number_format($total + $deliveryFee, 2) }}</strong>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-flora w-100">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
