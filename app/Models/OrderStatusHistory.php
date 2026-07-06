<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';

    public $timestamps = false;

    protected $fillable = ['order_id', 'status', 'updated_by', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return Order::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
