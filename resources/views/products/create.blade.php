<x-layout title="Create product">
    <h1>Create product</h1>

    <div class="form-card">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            @include('products._form')

            <p class="form-actions">
                <button class="btn" type="submit">Save</button>
                <a class="btn btn-secondary" href="{{ route('products.index') }}">Cancel</a>
            </p>
        </form>
    </div>
</x-layout>
