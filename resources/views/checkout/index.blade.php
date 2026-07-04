@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<h2 class="mb-4">Checkout</h2>
<form method="POST" action="{{ route('checkout.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-md-7">
            {{-- Delivery Address --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Delivery Address</h5></div>
                <div class="card-body">
                    @if($addresses->isEmpty())
                        <p class="text-muted">No saved addresses. <a href="{{ route('profile.show') }}">Add one in your profile.</a></p>
                    @else
                        @foreach($addresses as $addr)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="address_id" id="addr{{ $addr->id }}" value="{{ $addr->id }}" {{ old('address_id') == $addr->id ? 'checked' : '' }} required>
                                <label class="form-check-label" for="addr{{ $addr->id }}">
                                    <strong>{{ $addr->label }}</strong> — {{ $addr->street }}, {{ $addr->city }}, {{ $addr->postal_code }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                    @error('address_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Delivery Date --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Delivery Date</h5></div>
                <div class="card-body">
                    <label for="delivery_date" class="form-label">Select a date (1–14 days from today)</label>
                    <input type="date" class="form-control @error('delivery_date') is-invalid @enderror"
                        id="delivery_date" name="delivery_date"
                        min="{{ now()->addDay()->toDateString() }}"
                        max="{{ now()->addDays(14)->toDateString() }}"
                        value="{{ old('delivery_date') }}" required>
                    @error('delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Special Instructions --}}
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Special Handling Instructions</h5></div>
                <div class="card-body">
                    <textarea class="form-control" name="special_instructions" rows="3" maxlength="500"
                        placeholder="e.g. Handle with care, fragile leaves…">{{ old('special_instructions') }}</textarea>
                    <small class="text-muted">Max 500 characters</small>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Order Summary</h5></div>
                <div class="card-body">
                    @foreach($items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->product->common_name }} × {{ $item->quantity }}</span>
                            <span>PKR {{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal</span><span>PKR {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Delivery Fee</span><span>PKR {{ number_format($deliveryFee, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total (Cash on Delivery)</strong>
                        <strong class="text-success fs-5">PKR {{ number_format($total + $deliveryFee, 2) }}</strong>
                    </div>
                    <div class="alert alert-warning small">
                        <i class="bi bi-cash-coin me-1"></i> Pay cash upon delivery after inspecting your plants.
                    </div>
                    <button type="submit" class="btn btn-flora w-100 btn-lg">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
