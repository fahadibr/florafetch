<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f8fdf9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #2d6a4f; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .label { color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th { background: #f0f7f4; text-align: left; padding: 8px 12px; font-size: 13px; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: bold; background: #f0f7f4; }
        .footer { background: #f0f7f4; padding: 16px 32px; font-size: 12px; color: #888; text-align: center; }
        .badge { display: inline-block; background: #2d6a4f; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🌿 FloraFetch — Order Confirmed</h1>
        <p style="margin:4px 0 0;opacity:.85;">Thank you for your order, {{ $order->user->name }}!</p>
    </div>
    <div class="body">
        <p><span class="label">Order ID</span><br><strong>#{{ $order->id }}</strong></p>

        <p><span class="label">Order Date</span><br>{{ $order->created_at->format('d M Y, H:i') }}</p>

        <p><span class="label">Delivery Date</span><br>{{ $order->delivery_date->format('d M Y') }}</p>

        <p><span class="label">Delivery Address</span><br>
            {{ $order->delivery_address_snapshot['label'] }} —
            {{ $order->delivery_address_snapshot['street'] }},
            {{ $order->delivery_address_snapshot['city'] }},
            {{ $order->delivery_address_snapshot['postal_code'] }}
        </p>

        @if($order->special_instructions)
            <p><span class="label">Special Handling Instructions</span><br>{{ $order->special_instructions }}</p>
        @else
            <p><span class="label">Special Handling Instructions</span><br><em>None provided</em></p>
        @endif

        <p><span class="label">Items Ordered</span></p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name_snapshot }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>PKR {{ number_format($item->unit_price_snapshot, 2) }}</td>
                        <td>PKR {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3">Delivery Fee</td>
                    <td>PKR {{ number_format($order->delivery_fee, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3">Total (Cash on Delivery)</td>
                    <td>PKR {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="background:#fff8e1;border-left:4px solid #f59e0b;padding:12px 16px;border-radius:4px;margin-top:16px;">
            <strong>💵 Payment:</strong> Please have <strong>PKR {{ number_format($order->total_amount, 2) }}</strong> ready in cash at the time of delivery.
        </div>
    </div>
    <div class="footer">
        © {{ date('Y') }} FloraFetch — Bringing nature to your doorstep 🌱<br>
        If you have questions, reply to this email.
    </div>
</div>
</body>
</html>
