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
        <p class="logo">Sample Logo</p>
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
        <h2>Advertisement</h2>
        <p class="ad">
            <strong>Sample Ad</strong><br>
            This is placeholder advertising text for the right sidebar.
            Buy two products, get one free!
        </p>
        <p class="muted">Ad text is test content only.</p>
    </aside>

    <footer>
        <p>&copy; {{ date('Y') }} Sample Company. All rights reserved.</p>
    </footer>
</body>
</html>
