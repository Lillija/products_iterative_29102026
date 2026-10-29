<x-layout :title="'Edit ' . $product->name">
    <h1>Edit product</h1>

    <div class="form-card">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            @include('products._form')

            <p class="form-actions">
                <button class="btn" type="submit">Update</button>
                <a class="btn btn-secondary" href="{{ route('products.show', $product) }}">Cancel</a>
            </p>
        </form>
    </div>
</x-layout>
