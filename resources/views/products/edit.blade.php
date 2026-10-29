<x-layout :title="'Edit ' . $product->name">
    <h1>Edit product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        @include('products._form')

        <p>
            <button type="submit">Update</button>
            <a href="{{ route('products.show', $product) }}">Cancel</a>
        </p>
    </form>
</x-layout>
