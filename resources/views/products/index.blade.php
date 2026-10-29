<x-layout title="Products">
    <h1>Products</h1>

    <p>
        <a href="{{ route('products.create') }}" class="btn">Create product</a>
    </p>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <a href="{{ route('products.show', $product) }}">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td class="actions">
                            <a class="btn btn-secondary" href="{{ route('products.edit', $product) }}">Edit</a>

                            <form class="inline" action="{{ route('products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Delete &ldquo;{{ $product->name }}&rdquo;? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="empty-state" colspan="4">
                            No products yet.
                            <a href="{{ route('products.create') }}">Create the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->count() > 0)
        <p class="muted">
            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
        </p>
    @endif

    {{ $products->links('pagination::simple') }}
</x-layout>
