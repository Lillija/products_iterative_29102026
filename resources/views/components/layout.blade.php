<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>{{ $title ?? config('app.name') }}</title>

    <script>
        // Apply the saved or system theme before the CSS loads to avoid a flash
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('theme'); } catch (error) { /* storage unavailable */ }
            document.documentElement.dataset.theme =
                stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <p class="logo">Sample Logo</p>
        <button
            type="button"
            class="theme-toggle"
            data-theme-toggle
            aria-label="Toggle dark mode"
        >
            <span class="icon-moon" aria-hidden="true">🌙</span>
            <span class="icon-sun" aria-hidden="true">☀️</span>
        </button>
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
        <x-flash-message />

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
