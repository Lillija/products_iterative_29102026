<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Suggest tag names starting with the typed letters — feeds the
     * autocomplete box on the show view, hence the JSON response.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:50'],
        ]);

        $query = trim((string) ($validated['query'] ?? ''));

        $suggestions = Tag::query()
            ->when($query !== '', fn ($q) => $q->where('name', 'like', "{$query}%"))
            ->orderBy('name')
            ->limit(8)
            ->pluck('name');

        return response()->json(['suggestions' => $suggestions]);
    }
}
