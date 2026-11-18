@extends('layouts.app')

@section('title', $plant->plant_name)

@section('content')
<h3>{{ $plant->plant_name }}</h3>

<dl class="row">
    <dt class="col-sm-3">Category</dt>
    <dd class="col-sm-9">{{ $plant->category }}</dd>

    <dt class="col-sm-3">Unit price</dt>
    <dd class="col-sm-9">₱{{ number_format((float) $plant->unit_price, 2) }}</dd>

    <dt class="col-sm-3">Stock status</dt>
    <dd class="col-sm-9">
        @if($plant->stock_status === 'in_stock')
            <span class="badge bg-success">In Stock</span>
        @else
            <span class="badge bg-danger">Out of Stock</span>
        @endif
    </dd>

    <dt class="col-sm-3">Added by</dt>
    <dd class="col-sm-9">{{ $plant->user->name ?? 'Admin' }}</dd>

    <dt class="col-sm-3">Description</dt>
    <dd class="col-sm-9">{{ $plant->description }}</dd>

    <dt class="col-sm-3">Created</dt>
    <dd class="col-sm-9">{{ $plant->created_at?->format('M d, Y h:i A') }}</dd>

    <dt class="col-sm-3">Updated</dt>
    <dd class="col-sm-9">{{ $plant->updated_at?->format('M d, Y h:i A') }}</dd>
</dl>

<a href="{{ route('plants.edit', $plant) }}" class="btn btn-primary">Edit</a>
<form action="{{ route('plants.destroy', $plant) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this plant?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Delete</button>
</form>
<a href="{{ route('plants.index') }}" class="btn btn-secondary">Back</a>
@endsection
