<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlantController extends Controller
{
    /**
     * Display a listing of plant records with optional search/filter support.
     */
    public function index(Request $request): View
    {
        $query = Plant::with('user')->latest();

        // Optional quick search on the main inventory list
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('plant_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $plants = $query->paginate(10)->withQueryString();
        $categories = Plant::CATEGORIES;

        return view('plants.index', compact('plants', 'categories'));
    }

    /**
     * Show the form for creating a new plant record.
     */
    public function create(): View
    {
        $categories = Plant::CATEGORIES;
        return view('plants.create', compact('categories'));
    }

    /**
     * Validate and store a newly created plant record linked to the logged-in User.
     */
    public function store(Request $request): RedirectResponse
    {
        // Server-side validation: required fields, numeric & nonnegative unit_price (min:0)
        $validated = $request->validate([
            'plant_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(Plant::CATEGORIES)],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'stock_status' => ['required', Rule::in(['in_stock', 'out_of_stock'])],
        ]);

        // Create plant record via the authenticated User's hasMany relationship
        // Automatically populates the user_id foreign key
        Auth::user()->plants()->create($validated);

        return redirect()->route('plants.index')
            ->with('success', 'Plant record added to inventory successfully.');
    }

    /**
     * Display the specified plant record details.
     */
    public function show(Plant $plant): View
    {
        $plant->load('user');
        return view('plants.show', compact('plant'));
    }

    /**
     * Show the form for editing the specified plant record.
     */
    public function edit(Plant $plant): View
    {
        $categories = Plant::CATEGORIES;
        return view('plants.edit', compact('plant', 'categories'));
    }

    /**
     * Validate and update the specified plant record in storage.
     */
    public function update(Request $request, Plant $plant): RedirectResponse
    {
        // Server-side validation ensuring unit_price is numeric and nonnegative
        $validated = $request->validate([
            'plant_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(Plant::CATEGORIES)],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'stock_status' => ['required', Rule::in(['in_stock', 'out_of_stock'])],
        ]);

        $plant->update($validated);

        return redirect()->route('plants.index')
            ->with('success', 'Plant record updated successfully.');
    }

    /**
     * Remove the specified plant record from storage.
     */
    public function destroy(Plant $plant): RedirectResponse
    {
        $plant->delete();

        return redirect()->route('plants.index')
            ->with('success', 'Plant record deleted from inventory.');
    }
}
