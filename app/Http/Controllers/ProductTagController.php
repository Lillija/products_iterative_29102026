<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    /**
     * Replace the product's tags with the ones submitted from the
     * show view's tag editor, creating brand-new tag names as needed.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        $product->syncTags($validated['tags'] ?? []);

        return redirect()
            ->route('products.show', $product)
            ->with('status', 'Tags updated.');
    }
}
