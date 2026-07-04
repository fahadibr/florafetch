<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CatalogService
{
    public function search(string $query, array $filters = []): LengthAwarePaginator
    {
        $q = Product::active()->with(['category', 'primaryImage'])
            ->where(function (Builder $builder) use ($query) {
                $builder->where('common_name', $query)           // exact match first
                    ->orWhere('common_name', 'like', "%{$query}%")
                    ->orWhere('botanical_name', 'like', "%{$query}%")
                    ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$query}%"));
            })
            ->orderByRaw("
                CASE
                    WHEN common_name = ? THEN 1
                    WHEN common_name LIKE ? OR botanical_name LIKE ? THEN 2
                    ELSE 3
                END
            ", [$query, "%{$query}%", "%{$query}%"]);

        $q = $this->applyFilters($q, $filters);

        return $q->paginate(16);
    }

    public function browse(array $filters = [], ?int $categoryId = null): LengthAwarePaginator
    {
        $q = Product::active()->with(['category', 'primaryImage']);

        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }

        $q = $this->applyFilters($q, $filters);

        return $q->orderBy('common_name')->paginate(16);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['low_maintenance'])) {
            $query->where('is_low_maintenance', true);
        }

        if (!empty($filters['pet_friendly'])) {
            $query->where('is_pet_friendly', true);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        if (!empty($filters['growth_rate']) && in_array($filters['growth_rate'], ['Slow', 'Moderate', 'Fast'])) {
            $query->where('growth_rate', $filters['growth_rate']);
        }

        return $query;
    }
}
