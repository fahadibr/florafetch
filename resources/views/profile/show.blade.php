@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<h2 class="mb-4">My Profile</h2>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0">Personal Details</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-flora">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Delivery Addresses</h5>
                <small class="text-muted">{{ $addresses->count() }}/{{ config('florafetch.max_addresses') }}</small>
            </div>
            <div class="card-body">
                @foreach($addresses as $addr)
                    <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $addr->label }}</strong><br>
                            <small class="text-muted">{{ $addr->street }}, {{ $addr->city }}, {{ $addr->postal_code }}</small>
                        </div>
                        <form method="POST" action="{{ route('addresses.destroy', $addr) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete address"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                @endforeach

                @if($addresses->count() < config('florafetch.max_addresses'))
                    <hr>
                    <h6>Add New Address</h6>
                    <form method="POST" action="{{ route('addresses.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label for="label" class="form-label">Label</label>
                            <input type="text" class="form-control form-control-sm" id="label" name="label" placeholder="Home, Office…" maxlength="50" required>
                        </div>
                        <div class="mb-2">
                            <label for="street" class="form-label">Street</label>
                            <input type="text" class="form-control form-control-sm" id="street" name="street" required>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control form-control-sm" id="city" name="city" required>
                            </div>
                            <div class="col">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control form-control-sm" id="postal_code" name="postal_code" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-flora btn-sm">Add Address</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Plant History --}}
<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">🌿 Plant History</h5></div>
    <div class="card-body">
        @if($plantHistory->isEmpty())
            <p class="text-muted">No delivered orders yet.</p>
        @else
            @foreach($plantHistory as $order)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>Order #{{ $order->id }}</strong> — {{ $order->created_at->format('d M Y') }}<br>
                        <small class="text-muted">{{ $order->items->pluck('product_name_snapshot')->join(', ') }}</small>
                    </div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-success">View</a>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
