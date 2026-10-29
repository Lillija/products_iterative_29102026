<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
</head>
<body>
    <h1>Products</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <p>
        <a href="{{ route('products.create') }}">Create product</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
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
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}">Edit</a>

                        <form action="{{ route('products.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</body>
</html>
