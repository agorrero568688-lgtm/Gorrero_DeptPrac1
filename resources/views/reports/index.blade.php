@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Reports</h3>
    <div>
        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger">Export PDF</a>
        <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-success">Export Excel</a>
    </div>
</div>

<p>
    Total: {{ $summary['total_count'] }} |
    In stock: {{ $summary['in_stock_count'] }} |
    Out of stock: {{ $summary['out_of_stock_count'] }} |
    Average price: ₱{{ number_format((float) $summary['average_price'], 2) }}
</p>

<form action="{{ route('reports.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <label for="report_type" class="form-label">Report</label>
        <select name="report_type" id="report_type" class="form-select">
            <option value="all" {{ request('report_type', 'all') === 'all' ? 'selected' : '' }}>All plants</option>
            <option value="in_stock" {{ request('report_type') === 'in_stock' ? 'selected' : '' }}>In stock</option>
            <option value="out_of_stock" {{ request('report_type') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
            <option value="by_category" {{ request('report_type') === 'by_category' ? 'selected' : '' }}>By category</option>
            <option value="price_list" {{ request('report_type') === 'price_list' ? 'selected' : '' }}>Price list</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="category" class="form-label">Category</label>
        <select name="category" id="category" class="form-select">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label for="stock_status" class="form-label">Status</label>
        <select name="stock_status" id="stock_status" class="form-select">
            <option value="">All statuses</option>
            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In stock</option>
            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
        </select>
    </div>

    <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Added By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($plants as $plant)
        <tr>
            <td>{{ $plant->id }}</td>
            <td>{{ $plant->plant_name }}</td>
            <td>{{ $plant->category }}</td>
            <td>₱{{ number_format((float) $plant->unit_price, 2) }}</td>
            <td>
                @if($plant->stock_status === 'in_stock')
                    <span class="badge bg-success">In Stock</span>
                @else
                    <span class="badge bg-danger">Out of Stock</span>
                @endif
            </td>
            <td>{{ $plant->user->name ?? 'Admin' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No plants found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
