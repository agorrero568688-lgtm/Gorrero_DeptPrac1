@extends('layouts.app')

@section('title', 'Plants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Plants</h3>
    <div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reports</a>
        <a href="{{ route('plants.create') }}" class="btn btn-primary">Add Plant</a>
    </div>
</div>

<form action="{{ route('plants.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search plants" value="{{ request('search') }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('plants.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Added By</th>
            <th>Actions</th>
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
            <td>
                <a href="{{ route('plants.show', $plant) }}">View</a> |
                <a href="{{ route('plants.edit', $plant) }}">Edit</a> |
                <form action="{{ route('plants.destroy', $plant) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this plant?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 align-baseline">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No plants found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{ $plants->links('pagination::bootstrap-5') }}
@endsection
