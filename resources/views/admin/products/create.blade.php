@extends('layouts.admin')
@section('title', 'Add Product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Add New Product</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-success">Create Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
