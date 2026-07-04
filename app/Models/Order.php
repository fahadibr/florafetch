<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const PIPELINE = [
        'order_confirmed' => 'quality_check',
        'quality_check'   => 'in_transit',
        'in_transit'      => 'delivered',
    ];

    public const STATUS_LABELS = [
        'order_confirmed'  => 'Order Confirmed',
        'quality_check'    => 'Quality Check',
        'in_transit'       => 'In Transit',
        'delivered'        => 'Delivered',
        'delivery_refused' => 'Delivery Refused',
    ];

    protected $fillable = [
        'user_id', 'address_id', 'delivery_address_snapshot', 'delivery_date',
        'special_instructions', 'status', 'delivery_fee', 'total_amount',
        'estimated_delivery_date', 'delivered_at', 'refused_at', 'has_removed_listing',
    ];

    protected function casts(): array
    {
        return [
            'delivery_address_snapshot' => 'array',
            'delivery_date'             => 'date',
            'estimated_delivery_date'   => 'date',
            'delivered_at'              => 'datetime',
            'refused_at'                => 'datetime',
            'has_removed_listing'       => 'boolean',
            'total_amount'              => 'decimal:2',
            'delivery_fee'              => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getNextStatus(): ?string
    {
        return self::PIPELINE[$this->status] ?? null;
    }

    public function canAdvance(): bool
    {
        return isset(self::PIPELINE[$this->status]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
