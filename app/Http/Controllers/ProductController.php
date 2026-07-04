<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\CatalogService;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private CatalogService $catalogService,
        private ReviewService $reviewService,
    ) {}

    public function index(Request $request)
    {
        $categories = Category::all();
        $filters = $request->only(['low_maintenance', 'pet_friendly', 'price_min', 'price_max', 'growth_rate']);
        $categoryId = $request->input('category');

        $products = $this->catalogService->browse($filters, $categoryId);

        return view('catalog.index', compact('products', 'categories', 'filters'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'relatedProducts.primaryImage', 'approvedReviews.user']);
        $avgRating = $this->reviewService->getAverageRating($product);

        return view('catalog.show', compact('product', 'avgRating'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $filters = $request->only(['low_maintenance', 'pet_friendly', 'price_min', 'price_max', 'growth_rate']);
        $categories = Category::all();

        $products = $query
            ? $this->catalogService->search($query, $filters)
            : $this->catalogService->browse($filters);

        return view('catalog.index', compact('products', 'categories', 'filters', 'query'));
    }
}
