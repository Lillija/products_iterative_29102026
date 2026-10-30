<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <h1>Products app</h1>
    </header>

    <nav aria-label="Main navigation">
        <ul class="nav-list">
            <li>
                <a
                    href="{{ route('products.index') }}"
                    class="nav-link{{ request()->routeIs('products.index') ? ' is-active' : '' }}"
                    @if (request()->routeIs('products.index')) aria-current="page" @endif
                >Products</a>
            </li>
            <li>
                <a
                    href="{{ route('products.create') }}"
                    class="nav-link{{ request()->routeIs('products.create') ? ' is-active' : '' }}"
                    @if (request()->routeIs('products.create')) aria-current="page" @endif
                >Create product</a>
            </li>
        </ul>
    </nav>

    <main>
        @if (session('status'))
            <div class="alert alert-success" role="status" data-auto-dismiss>
                {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <aside>
        <h2>Tips</h2>
        <p>Browse, create, edit and delete products from the menu on the left.</p>
        <p class="muted">Tip: click a product name to see its details.</p>
    </aside>

    <footer>
        <p>products-iterative — Laravel {{ app()->version() }}</p>
    </footer>
</body>
</html>
