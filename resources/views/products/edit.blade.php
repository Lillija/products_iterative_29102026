<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $product->name }}</title>
</head>
<body>
    <h1>Edit product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            @error('name')
                <span>{{ $message }}</span>
            @enderror
        </p>

        <p>
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <span>{{ $message }}</span>
            @enderror
        </p>

        <p>
            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
            @error('price')
                <span>{{ $message }}</span>
            @enderror
        </p>

        <p>
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="0" value="{{ old('quantity', $product->quantity) }}" required>
            @error('quantity')
                <span>{{ $message }}</span>
            @enderror
        </p>

        <p>
            <button type="submit">Update</button>
            <a href="{{ route('products.show', $product) }}">Cancel</a>
        </p>
    </form>
</body>
</html>
