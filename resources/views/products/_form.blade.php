<x-validation-errors />

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
    <label for="expiration_date">Expiration date</label>
    <input
        type="date"
        id="expiration_date"
        name="expiration_date"
        value="{{ old('expiration_date', isset($product) && $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '') }}"
        @error('expiration_date') class="input-invalid" @enderror
    >
    @error('expiration_date')
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>

<div class="form-field">
    <label for="status">Status</label>
    <select
        id="status"
        name="status"
        @error('status') class="input-invalid" @enderror
    >
        @foreach (\App\Enums\ProductStatus::options() as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $product->status->value ?? 'active') === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('status')
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
