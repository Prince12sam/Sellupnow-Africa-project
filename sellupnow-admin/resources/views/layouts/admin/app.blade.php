<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - {{ config('app.name') }}</title>
    @stack('styles')
</head>
<body>
    <div class="admin-app">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
