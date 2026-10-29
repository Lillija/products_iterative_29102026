<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav>
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('products.create') }}">Create product</a>
    </nav>

    <main>
        {{ $slot }}
    </main>
</body>
</html>
