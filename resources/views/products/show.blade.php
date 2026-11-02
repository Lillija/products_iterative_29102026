<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>

    <x-validation-errors />

    <div class="notifications" data-notifications></div>

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
            <dd class="stock-control">
                <span class="stock-value" data-quantity>{{ $product->quantity }} pcs</span>

                <form class="stock-form" id="stock-form"
                      action="{{ route('products.change-quantity', $product) }}" method="POST">
                    @csrf
                </form>

                <input class="stock-amount" form="stock-form" type="number" name="amount"
                       value="1" min="1" max="100000" aria-label="Amount to add or remove">

                <button class="btn btn-stock" form="stock-form" type="submit"
                        name="action" value="increase">+ Delivery</button>

                <button class="btn btn-stock btn-stock-out" form="stock-form" type="submit"
                        name="action" value="decrease">− Purchase</button>
            </dd>

            <dt>Tags</dt>
            <dd>
                {{-- The Update button is a classic full-page PUT (no JS needed);
                    the editor's chips submit as hidden inputs named tags[]. --}}
                <form id="tag-form" action="{{ route('products.tags.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="tag-editor" data-tag-editor
                         data-tags-search-url="{{ route('tags.search') }}">
                        <ul class="tag-list" data-tag-list>
                            @foreach ($tags as $tag)
                                <li class="tag-chip" data-tag-chip>
                                    {{ $tag->name }}
                                    <button class="tag-remove" type="button" data-tag-remove
                                            aria-label="Remove tag {{ $tag->name }}">&times;</button>
                                    <input type="hidden" name="tags[]" value="{{ $tag->name }}">
                                </li>
                            @endforeach
                        </ul>

                        <input class="tag-input" type="text" data-tag-input autocomplete="off"
                               placeholder="Add a tag and press Enter…" aria-label="Add a tag">

                        <ul class="tag-suggestions" data-tag-suggestions hidden></ul>

                        <button class="btn" type="submit">Update</button>
                    </div>
                </form>
            </dd>
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
