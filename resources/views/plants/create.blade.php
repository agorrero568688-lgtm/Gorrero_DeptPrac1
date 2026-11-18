@extends('layouts.app')

@section('title', 'Add Plant')

@section('content')
<h3>Add Plant</h3>

<form action="{{ route('plants.store') }}" method="POST" class="col-md-6">
    @csrf

    <div class="mb-3">
        <label for="plant_name" class="form-label">Plant name</label>
        <input type="text" name="plant_name" id="plant_name" class="form-control @error('plant_name') is-invalid @enderror" value="{{ old('plant_name') }}" required>
        @error('plant_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="unit_price" class="form-label">Unit price (₱)</label>
        <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" required>
        @error('unit_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="stock_status" class="form-label">Stock status</label>
        <select name="stock_status" id="stock_status" class="form-select @error('stock_status') is-invalid @enderror" required>
            <option value="in_stock" {{ old('stock_status', 'in_stock') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
            <option value="out_of_stock" {{ old('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
        @error('stock_status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('plants.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
