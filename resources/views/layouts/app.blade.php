<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>SALES INVOICE</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('cdn.head')
</head>
<body>
    <div id="loading">
        <strong style="font-size: 40px;">PLEASE WAIT...</strong><br>
        <div style="zoom: 400%;" class="spinner-border"></div><br>
        <strong style="font-size: 25px;">
            <i class='fa fa-exclamation-triangle'></i>
            Please DO NOT interrupt or cancel this process.
        </strong>
    </div>
    @if(Auth::guest())
        @include('inc.guest')
    @else
        <script>
            $('#loading').show();
        </script>
        @include('inc.navbar')
        {{-- @include('inc.include') --}}
    @endif
    @if(!Auth::guest())
        <script src="/js/functions.js?ver={{\Illuminate\Support\Str::random(50)}}"></script>
    @endif
    <main class="container-fluid content">
        @yield('content')
    </main>
    @include('cdn.body')
</body>
</html>