@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Edit: {{ $product->common_name }}</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.products._form')

            {{-- Existing Images --}}
            @if($product->images->isNotEmpty())
                <div class="mt-4">
                    <label class="form-label fw-semibold">Current Images</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $img)
                            <img src="{{ $img->url }}" alt="Product image" style="width:80px;height:80px;object-fit:cover;" class="rounded border">
                        @endforeach
                    </div>
                    <small class="text-muted">New uploads will be added alongside existing images.</small>
                </div>
            @endif

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
