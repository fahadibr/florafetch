<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'common_name', 'botanical_name', 'description',
        'size', 'price', 'stock_quantity', 'sunlight_requirement',
        'watering_frequency', 'soil_recommendation', 'temperature_min_c',
        'temperature_max_c', 'is_low_maintenance', 'is_pet_friendly',
        'growth_rate', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'             => 'decimal:2',
            'is_low_maintenance' => 'boolean',
            'is_pet_friendly'   => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('sort_order');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_related',
            'product_id',
            'related_product_id'
        )->limit(5);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
