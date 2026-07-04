<div class="row g-3">
    <div class="col-md-6">
        <label for="common_name" class="form-label">Common Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('common_name') is-invalid @enderror" id="common_name" name="common_name"
               value="{{ old('common_name', $product->common_name ?? '') }}" required>
        @error('common_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="botanical_name" class="form-label">Botanical Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('botanical_name') is-invalid @enderror" id="botanical_name" name="botanical_name"
               value="{{ old('botanical_name', $product->botanical_name ?? '') }}" required>
        @error('botanical_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Select category…</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="size" class="form-label">Size <span class="text-danger">*</span></label>
        <select class="form-select @error('size') is-invalid @enderror" id="size" name="size" required>
            <option value="">Select size…</option>
            @foreach(['small','medium','large'] as $s)
                <option value="{{ $s }}" {{ old('size', $product->size ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="growth_rate" class="form-label">Growth Rate</label>
        <select class="form-select" id="growth_rate" name="growth_rate">
            <option value="">Not specified</option>
            @foreach(['Slow','Moderate','Fast'] as $gr)
                <option value="{{ $gr }}" {{ old('growth_rate', $product->growth_rate ?? '') === $gr ? 'selected' : '' }}>{{ $gr }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="price" class="form-label">Price (PKR) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
               value="{{ old('price', $product->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
        <input type="number" min="0" class="form-control @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity"
               value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
        @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="sunlight_requirement" class="form-label">Sunlight Requirement <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('sunlight_requirement') is-invalid @enderror" id="sunlight_requirement" name="sunlight_requirement"
               value="{{ old('sunlight_requirement', $product->sunlight_requirement ?? '') }}" placeholder="e.g. Full Sun" required>
        @error('sunlight_requirement')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="watering_frequency" class="form-label">Watering Frequency <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('watering_frequency') is-invalid @enderror" id="watering_frequency" name="watering_frequency"
               value="{{ old('watering_frequency', $product->watering_frequency ?? '') }}" placeholder="e.g. Twice a week" required>
        @error('watering_frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="temperature_min_c" class="form-label">Min Temp (°C)</label>
        <input type="number" step="0.1" class="form-control" id="temperature_min_c" name="temperature_min_c"
               value="{{ old('temperature_min_c', $product->temperature_min_c ?? '') }}">
    </div>
    <div class="col-md-4">
        <label for="temperature_max_c" class="form-label">Max Temp (°C)</label>
        <input type="number" step="0.1" class="form-control" id="temperature_max_c" name="temperature_max_c"
               value="{{ old('temperature_max_c', $product->temperature_max_c ?? '') }}">
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label for="soil_recommendation" class="form-label">Soil Recommendation</label>
        <textarea class="form-control" id="soil_recommendation" name="soil_recommendation" rows="2">{{ old('soil_recommendation', $product->soil_recommendation ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="is_low_maintenance" name="is_low_maintenance" value="1"
                   {{ old('is_low_maintenance', $product->is_low_maintenance ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_low_maintenance">Low Maintenance</label>
        </div>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="is_pet_friendly" name="is_pet_friendly" value="1"
                   {{ old('is_pet_friendly', $product->is_pet_friendly ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_pet_friendly">Pet Friendly</label>
        </div>
    </div>
    <div class="col-md-6">
        <label for="images" class="form-label">Product Images (JPEG/PNG, max 5MB each)</label>
        <input type="file" class="form-control" id="images" name="images[]" accept="image/jpeg,image/png,image/jpg" multiple>
        <small class="text-muted">You can select multiple images.</small>
    </div>
</div>
