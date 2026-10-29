<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
</head>
<body>
    <h1>{{ $product->name }}</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <dl>
        <dt>Description</dt>
        <dd>{{ $product->description ?? '—' }}</dd>

        <dt>Price</dt>
        <dd>{{ $product->price }}</dd>

        <dt>Quantity</dt>
        <dd>{{ $product->quantity }}</dd>
    </dl>

    <p>
        <a href="{{ route('products.edit', $product) }}">Edit</a>

        <form action="{{ route('products.destroy', $product) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>

        <a href="{{ route('products.index') }}">Back to products</a>
    </p>
</body>
</html>
