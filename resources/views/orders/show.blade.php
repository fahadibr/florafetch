@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Order #{{ $order->id }}</h2>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">← My Orders</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        {{-- Status Timeline --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h5 class="mb-0">Order Status</h5></div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                        @php
                            $history = $order->statusHistory->firstWhere('status', $key);
                            $isCurrent = $order->status === $key;
                            $isDone = $history !== null;
                        @endphp
                        <div class="text-center" style="min-width:100px;">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1
                                {{ $isCurrent ? 'bg-success text-white' : ($isDone ? 'bg-light border border-success text-success' : 'bg-light text-muted') }}"
                                style="width:40px;height:40px;">
                                @if($isDone) ✓ @else ○ @endif
                            </div>
                            <div class="small fw-semibold {{ $isCurrent ? 'text-success' : '' }}">{{ $label }}</div>
                            @if($history)
                                <div class="text-muted" style="font-size:0.7rem;">{{ $history->created_at->format('d M, H:i') }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($order->status === 'in_transit')
                    @if($order->estimated_delivery_date)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-truck me-2"></i>Estimated delivery: <strong>{{ $order->estimated_delivery_date->format('d M Y') }}</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">Estimated delivery date not yet available.</div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Order Items --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h5 class="mb-0">Items</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ $item->product->primaryImage->url }}" alt="{{ $item->product_name_snapshot }}" style="width:50px;height:50px;object-fit:cover;" class="rounded me-2">
                                    @endif
                                    {{ $item->product_name_snapshot }}
                                </td>
                                <td>× {{ $item->quantity }}</td>
                                <td class="text-end">PKR {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr><td colspan="2">Delivery Fee</td><td class="text-end">PKR {{ number_format($order->delivery_fee, 2) }}</td></tr>
                        <tr><td colspan="2"><strong>Total (Cash on Delivery)</strong></td><td class="text-end fw-bold text-success">PKR {{ number_format($order->total_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Review Forms --}}
        @if($order->status === 'delivered' && !empty($reviewableProductIds))
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0">Leave a Review</h5></div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        @if($item->product_id && in_array($item->product_id, $reviewableProductIds))
                            <h6>{{ $item->product_name_snapshot }}</h6>
                            <form method="POST" action="{{ route('reviews.store', $order) }}" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <div class="mb-2">
                                    <label class="form-label">Rating</label>
                                    <select name="rating" class="form-select form-select-sm w-auto" required aria-label="Rating">
                                        @for($r = 5; $r >= 1; $r--)
                                            <option value="{{ $r }}">{{ str_repeat('★', $r) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Comment (optional)</label>
                                    <textarea name="comment" class="form-control" rows="2" maxlength="1000"></textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Photo (optional, JPEG/PNG, max 5MB)</label>
                                    <input type="file" name="photo" class="form-control form-control-sm" accept="image/jpeg,image/png">
                                </div>
                                <button type="submit" class="btn btn-flora btn-sm">Submit Review</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Delivery Info --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header"><h6 class="mb-0">Delivery Address</h6></div>
            <div class="card-body">
                @php $addr = $order->delivery_address_snapshot; @endphp
                <strong>{{ $addr['label'] }}</strong><br>
                {{ $addr['street'] }}<br>
                {{ $addr['city'] }}, {{ $addr['postal_code'] }}
            </div>
        </div>
        <div class="card shadow-sm mb-3">
            <div class="card-header"><h6 class="mb-0">Delivery Date</h6></div>
            <div class="card-body">{{ $order->delivery_date->format('d M Y') }}</div>
        </div>
        @if($order->special_instructions)
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="mb-0">Special Instructions</h6></div>
                <div class="card-body">{{ $order->special_instructions }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
