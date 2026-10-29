<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validateProduct($request));

        return redirect()
            ->route('products.show', $product)
            ->with('status', 'Product created.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validateProduct($request));

        return redirect()
            ->route('products.show', $product)
            ->with('status', 'Product updated.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Product deleted.');
    }

    /**
     * Validate the incoming product payload.
     *
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);
    }
}
