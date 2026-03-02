@php
    $generaleSetting = App\Models\GeneraleSetting::first();

    $title = $generaleSetting?->title ?? config('app.name', 'Sellupnow');
    $favicon = $generaleSetting?->favicon ?? asset('assets/favicon.png');
    $viteManifest = public_path('build/manifest.json');
    $canLoadVite = file_exists($viteManifest);
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="app-url" content="{{ url('/') }}">
    <!-- description -->
    <meta name="description" content="ecommerce website">

    <title>{{ $title }}</title>
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">

    @if ($canLoadVite)
        @vite('resources/css/app.css')
    @endif
</head>

<body>
    <div id="app"></div>

    @if ($canLoadVite)
        @vite('resources/js/app.js')
    @endif
</body>

</html>
