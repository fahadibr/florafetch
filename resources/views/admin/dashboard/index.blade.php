@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<h3 class="mb-4">Dashboard</h3>

<div class="row g-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3"><i class="bi bi-bag-check fs-3 text-success"></i></div>
                <div>
                    <div class="text-muted small">Total Orders</div>
                    <div class="fs-4 fw-bold">{{ number_format($metrics['totalOrders']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3"><i class="bi bi-cash-stack fs-3 text-primary"></i></div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="fs-4 fw-bold">PKR {{ number_format($metrics['totalRevenue'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3"><i class="bi bi-hourglass-split fs-3 text-warning"></i></div>
                <div>
                    <div class="text-muted small">Pending Orders</div>
                    <div class="fs-4 fw-bold">{{ ($metrics['ordersByStatus']['order_confirmed'] ?? 0) + ($metrics['ordersByStatus']['quality_check'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 p-3"><i class="bi bi-truck fs-3 text-info"></i></div>
                <div>
                    <div class="text-muted small">In Transit</div>
                    <div class="fs-4 fw-bold">{{ $metrics['ordersByStatus']['in_transit'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Orders by Status --}}
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Orders by Status</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-end fw-bold">{{ $metrics['ordersByStatus'][$key] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Orders</span>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>PKR {{ number_format($order->total_amount, 0) }}</td>
                                <td><span class="badge bg-secondary">{{ $order->status_label }}</span></td>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-xs btn-outline-secondary btn-sm py-0">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
