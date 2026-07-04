@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Orders</h3>
</div>

{{-- Status Filter --}}
<form method="GET" class="d-flex gap-2 mb-4">
    <select name="status" class="form-select form-select-sm w-auto" aria-label="Filter by status">
        <option value="">All Statuses</option>
        @foreach($statuses as $key => $label)
            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-sm btn-outline-success">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Delivery Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Flag</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->delivery_date->format('d M Y') }}</td>
                        <td>PKR {{ number_format($order->total_amount, 0) }}</td>
                        <td>
                            @php
                                $badgeClass = match($order->status) {
                                    'delivered' => 'bg-success',
                                    'in_transit' => 'bg-primary',
                                    'quality_check' => 'bg-warning text-dark',
                                    'delivery_refused' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $order->status_label }}</span>
                        </td>
                        <td>
                            @if($order->has_removed_listing)
                                <span class="badge bg-danger" title="Contains a removed listing">⚠ Listing Removed</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-success">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
