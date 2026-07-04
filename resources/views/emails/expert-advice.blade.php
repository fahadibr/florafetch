<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; background: #f8fdf9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #2d6a4f; color: #fff; padding: 24px 32px; }
        .body { padding: 32px; }
        .advice-box { background: #f0f7f4; border-left: 4px solid #2d6a4f; padding: 16px 20px; border-radius: 4px; margin: 16px 0; }
        .footer { background: #f0f7f4; padding: 16px 32px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🌿 FloraFetch — Expert Advice on Your Review</h1>
        <p style="margin:4px 0 0;opacity:.85;">Hi {{ $review->user->name }}, our plant expert has responded to your review!</p>
    </div>
    <div class="body">
        <p>You reviewed <strong>{{ $review->product->common_name }}</strong>
            ({{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}):</p>

        @if($review->comment)
            <blockquote style="border-left:3px solid #ccc;padding-left:12px;color:#666;font-style:italic;">
                "{{ $review->comment }}"
            </blockquote>
        @endif

        <p><strong>Our Expert's Response:</strong></p>
        <div class="advice-box">
            {{ $review->expert_advice }}
        </div>

        <p style="margin-top:24px;">
            <a href="{{ config('app.url') }}/catalog/{{ $review->product_id }}"
               style="background:#2d6a4f;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;display:inline-block;">
                View Plant Page
            </a>
        </p>
    </div>
    <div class="footer">
        © {{ date('Y') }} FloraFetch — Bringing nature to your doorstep 🌱
    </div>
</div>
</body>
</html>
