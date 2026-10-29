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
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('products.create') }}">Create product</a>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <aside>
        <h2>Sidebar</h2>
        <p>Holy grail layout implemented with CSS Grid.</p>
    </aside>

    <footer>
        <p>products-iterative — Laravel {{ app()->version() }}</p>
    </footer>
</body>
</html>
