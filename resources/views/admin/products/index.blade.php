@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Products</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i>Import CSV
        </button>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <select name="category" class="form-select form-select-sm" aria-label="Filter by category">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="stock" class="form-select form-select-sm" aria-label="Filter by stock">
            <option value="">All Stock</option>
            <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-success">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $product->common_name }}</div>
                            <small class="text-muted fst-italic">{{ $product->botanical_name }}</small>
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td>PKR {{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                            @else
                                <span class="badge bg-danger">Out of Stock</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($product->size) }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                  onsubmit="return confirm('Remove this product from catalog?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->withQueryString()->links() }}</div>

{{-- CSV Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">CSV must have columns: <code>common_name, botanical_name, price, size, category, sunlight_requirement, watering_frequency</code> (optional: stock_quantity, is_low_maintenance, is_pet_friendly, growth_rate)</p>
                    <div class="mb-3">
                        <label for="csv" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csv" name="csv" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
