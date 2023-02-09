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

        <!-- Scripts -->
        @vite([
            'resources/sass/app.scss',
            'resources/js/app.js',
            'resources/css/all.min.css',
            'resources/css/layouts/common.css',
            'resources/css/layouts/header.css',
            'resources/css/layouts/sidebar.css'
        ])

        @stack('css')

        @inject('routeService', 'App\Services\RouteService')
    </head>
    <body>
        @include('layouts.header')

        <main>
            @include('layouts.sidebar')

            <div class="main-contents">
                <div class="container bg-white rounded-2 p-3 h-100">
                    @yield('content')
                </div>
            </div>
        </main>
    </body>

    @stack('jquery')
    @vite('resources/js/common.js')
</html>
