<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <div class="card">
        <dl class="product-details">
            <dt>Description</dt>
            <dd>{{ $product->description ?? '—' }}</dd>

            <dt>Status</dt>
            <dd>
                <span class="status-badge status-{{ $product->status->value }}">
                    {{ $product->status->label() }}
                </span>
            </dd>

            <dt>Expiration date</dt>
            <dd>{{ $product->expiration_date?->format('Y-m-d') ?? '—' }}</dd>

            <dt>Quantity</dt>
            <dd>{{ $product->quantity }} pcs</dd>
        </dl>

        <p class="form-actions">
            <a class="btn btn-secondary" href="{{ route('products.edit', $product) }}">Edit</a>

            <form class="inline" action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Delete &ldquo;{{ $product->name }}&rdquo;? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">Delete</button>
            </form>

            <a class="btn btn-secondary" href="{{ route('products.index') }}">Back to products</a>
        </p>
    </div>
</x-layout>
