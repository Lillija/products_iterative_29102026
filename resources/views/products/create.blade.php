<x-layout title="Create product">
    <h1>Create product</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        @include('products._form')

        <p>
            <button type="submit">Save</button>
            <a href="{{ route('products.index') }}">Cancel</a>
        </p>
    </form>
</x-layout>
