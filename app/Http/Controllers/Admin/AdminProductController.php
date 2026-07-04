<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function __construct(private CsvImportService $csvImportService) {}

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock')) {
            $query->when($request->stock === 'in_stock', fn($q) => $q->where('stock_quantity', '>', 0));
            $query->when($request->stock === 'out_of_stock', fn($q) => $q->where('stock_quantity', 0));
        }

        $products = $query->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path, 'sort_order' => $i]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);
        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path, 'sort_order' => $product->images()->count() + $i]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);

        // Flag open orders
        Order::whereIn('status', ['order_confirmed', 'quality_check', 'in_transit'])
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->update(['has_removed_listing' => true]);

        return redirect()->route('admin.products.index')->with('success', 'Product removed from catalog.');
    }

    public function import(Request $request)
    {
        $request->validate(['csv' => 'required|file|mimes:csv,txt']);

        $path = $request->file('csv')->getRealPath();
        $results = $this->csvImportService->import($path);

        $message = "Imported {$results['imported']} product(s).";
        if (!empty($results['errors'])) {
            $message .= ' Errors: ' . implode(' | ', $results['errors']);
        }

        return back()->with('success', $message);
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'common_name'          => 'required|string|max:255',
            'botanical_name'       => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'price'                => 'required|numeric|gt:0',
            'size'                 => 'required|in:small,medium,large',
            'stock_quantity'       => 'required|integer|min:0',
            'sunlight_requirement' => 'required|string|max:100',
            'watering_frequency'   => 'required|string|max:100',
            'description'          => 'nullable|string',
            'soil_recommendation'  => 'nullable|string',
            'temperature_min_c'    => 'nullable|numeric',
            'temperature_max_c'    => 'nullable|numeric',
            'is_low_maintenance'   => 'boolean',
            'is_pet_friendly'      => 'boolean',
            'growth_rate'          => 'nullable|in:Slow,Moderate,Fast',
            'images.*'             => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);
    }
}
