<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/icon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icon.ico') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/icon.ico') }}">

        <!-- Scripts -->
        @if (config('app.os_type') === 'Windows')
        @vite([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/css/all.min.css',
            'resources/css/layouts/common.css',
            'resources/css/layouts/header.css',
            'resources/css/layouts/sidebar.css'
        ])
        @elseif('Linux')
        <script src="https://kit.fontawesome.com/b36b80b928.js" crossorigin="anonymous"></script>

        @vite([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/css/layouts/common.css',
            'resources/css/layouts/header.css',
            'resources/css/layouts/sidebar.css'
        ])
        @endif

        @stack('css')

        @inject('routeService', 'App\Services\RouteService')
    </head>
    <body>
        @include('layouts.header')

        <main>
            @if (auth()->check())
            @include('layouts.sidebar')

            <div class="main-contents">
                <div class="container bg-white rounded-2 p-3 h-100">
                    @yield('content')
                </div>
            </div>
            @else
            @yield('auth')
            @endif
        </main>
    </body>

    @stack('jquery')
    @vite('resources/js/common.js')
</html>
