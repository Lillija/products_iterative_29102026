@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <strong>Please fix the following {{ Str::plural('error', $errors->count()) }}:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-field">
    <label for="name">Name</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $product->name ?? '') }}"
        @error('name') class="input-invalid" @enderror
        required
    >
    @error('name')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

<div class="form-field">
    <label for="description">Description</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        @error('description') class="input-invalid" @enderror
    >{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

<div class="form-field">
    <label for="price">Price</label>
    <input
        type="number"
        id="price"
        name="price"
        step="0.01"
        min="0"
        value="{{ old('price', $product->price ?? '') }}"
        @error('price') class="input-invalid" @enderror
        required
    >
    @error('price')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

<div class="form-field">
    <label for="quantity">Quantity</label>
    <input
        type="number"
        id="quantity"
        name="quantity"
        min="0"
        value="{{ old('quantity', $product->quantity ?? '') }}"
        @error('quantity') class="input-invalid" @enderror
        required
    >
    @error('quantity')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
