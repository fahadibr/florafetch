@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<h2 class="mb-4">My Orders</h2>

@if($orders->isEmpty())
    <div class="alert alert-info">You haven't placed any orders yet. <a href="{{ route('catalog.index') }}">Start shopping!</a></div>
@else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->items->count() }} item(s)</td>
                        <td>PKR {{ number_format($order->total_amount, 2) }}</td>
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
                        <td><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-success">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $orders->links() }}
@endif
@endsection
