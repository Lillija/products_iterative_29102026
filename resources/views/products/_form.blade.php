<p>
    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
    @error('name')
        <span>{{ $message }}</span>
    @enderror
</p>

<p>
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <span>{{ $message }}</span>
    @enderror
</p>

<p>
    <label for="price">Price</label>
    <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required>
    @error('price')
        <span>{{ $message }}</span>
    @enderror
</p>

<p>
    <label for="quantity">Quantity</label>
    <input type="number" id="quantity" name="quantity" min="0" value="{{ old('quantity', $product->quantity ?? '') }}" required>
    @error('quantity')
        <span>{{ $message }}</span>
    @enderror
</p>
