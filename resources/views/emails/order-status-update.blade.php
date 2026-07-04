<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f8fdf9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #2d6a4f; color: #fff; padding: 24px 32px; }
        .body { padding: 32px; }
        .status-badge { display: inline-block; background: #2d6a4f; color: #fff; padding: 8px 20px; border-radius: 20px; font-size: 16px; font-weight: bold; margin: 12px 0; }
        .footer { background: #f0f7f4; padding: 16px 32px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🌿 FloraFetch — Order Update</h1>
        <p style="margin:4px 0 0;opacity:.85;">Hi {{ $order->user->name }}, your order status has changed.</p>
    </div>
    <div class="body">
        <p><strong>Order #{{ $order->id }}</strong></p>
        <p>Your order status is now:</p>
        <div class="status-badge">{{ $order->status_label }}</div>

        @if($order->status === 'in_transit')
            @if($order->estimated_delivery_date)
                <p>🚚 <strong>Estimated Delivery:</strong> {{ $order->estimated_delivery_date->format('d M Y') }}</p>
            @else
                <p>🚚 Your order is on its way! Estimated delivery date will be updated soon.</p>
            @endif
        @elseif($order->status === 'delivered')
            <p>✅ Your order has been delivered. We hope your plants are thriving!</p>
            <p>Don't forget to leave a review and share how your plants are doing in their new home.</p>
        @elseif($order->status === 'quality_check')
            <p>🔍 Our team is carefully inspecting and packaging your plants for safe travel.</p>
        @endif

        <p style="margin-top:24px;">
            <a href="{{ config('app.url') }}/orders/{{ $order->id }}"
               style="background:#2d6a4f;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;display:inline-block;">
                View Order Details
            </a>
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} FloraFetch — Bringing nature to your doorstep 🌱
    </div>
</div>
</body>
</html>
