@extends('layouts.admin')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Order #{{ $order->id }}</h3>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Orders</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        {{-- Status Update --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Update Order Status</div>
            <div class="card-body">
                <p>Current status: <strong>{{ $order->status_label }}</strong></p>

                @if($order->canAdvance())
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="d-flex flex-wrap gap-3 align-items-end">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="advance">
                        @if($order->getNextStatus() === 'in_transit')
                            <div>
                                <label for="estimated_delivery_date" class="form-label">Estimated Delivery Date</label>
                                <input type="date" class="form-control form-control-sm" id="estimated_delivery_date"
                                       name="estimated_delivery_date" min="{{ now()->addDay()->toDateString() }}">
                            </div>
                        @endif
                        <button type="submit" class="btn btn-success">
                            Advance to: {{ \App\Models\Order::STATUS_LABELS[$order->getNextStatus()] }}
                        </button>
                    </form>
                @else
                    <p class="text-muted mb-0">This order is at its final status.</p>
                @endif

                @if(!in_array($order->status, ['delivered', 'delivery_refused']))
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-3"
                          onsubmit="return confirm('Mark this order as Delivery Refused?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="refuse">
                        <button type="submit" class="btn btn-outline-danger btn-sm">Mark as Delivery Refused</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Status History --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Status History</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Status</th><th>Updated By</th><th>Date & Time</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->statusHistory as $history)
                            <tr>
                                <td>{{ \App\Models\Order::STATUS_LABELS[$history->status] ?? $history->status }}</td>
                                <td>{{ $history->updatedBy->name }}</td>
                                <td>{{ $history->created_at->format('d M Y, H:i') }} UTC</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">
                Items
                @if($order->has_removed_listing)
                    <span class="badge bg-danger ms-2">⚠ Contains Removed Listing</span>
                @endif
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    {{ $item->product_name_snapshot }}
                                    @if(!$item->product_id)
                                        <span class="badge bg-danger ms-1">Removed</span>
                                    @endif
                                </td>
                                <td>PKR {{ number_format($item->unit_price_snapshot, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>PKR {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr><td colspan="3">Delivery Fee</td><td>PKR {{ number_format($order->delivery_fee, 2) }}</td></tr>
                        <tr><td colspan="3"><strong>Total (COD)</strong></td><td><strong>PKR {{ number_format($order->total_amount, 2) }}</strong></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        {{-- Customer Info --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Customer</div>
            <div class="card-body">
                <strong>{{ $order->user->name }}</strong><br>
                @if($order->user->email)<small>{{ $order->user->email }}</small><br>@endif
                @if($order->user->phone)<small>{{ $order->user->phone }}</small>@endif
            </div>
        </div>

        {{-- Delivery Info --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Delivery Address</div>
            <div class="card-body">
                @php $addr = $order->delivery_address_snapshot; @endphp
                <strong>{{ $addr['label'] }}</strong><br>
                {{ $addr['street'] }}<br>
                {{ $addr['city'] }}, {{ $addr['postal_code'] }}
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Delivery Date</div>
            <div class="card-body">{{ $order->delivery_date->format('d M Y') }}</div>
        </div>

        @if($order->special_instructions)
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Special Instructions</div>
                <div class="card-body">{{ $order->special_instructions }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
