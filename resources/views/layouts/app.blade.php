<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{env('APP_NAME')}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(Request::is('login'))
        <meta http-equiv="refresh" content="300;url={{ url('/login') }}">
    @endif
    <link href="{{asset('image/idsi.ico')}}" rel="icon" type="image/x-icon"/>
    <link href="{{asset('image/idsi.ico')}}" rel="shortcut icon" type="image/x-icon"/>
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
        @include('inc.include')
    @endif
    @if(!Auth::guest())
        <script src="/js/functions.js?ver={{\Illuminate\Support\Str::random(50)}}"></script>
        @if(Request::is('si') || Request::is('cr') || Request::is('bs') || Request::is('or') || Request::is('dr'))
            <script src="/js/generic.js?ver={{\Illuminate\Support\Str::random(50)}}"></script>
            <style>
                .filter-input{
                    display:none;
                }
            </style>
        @endif
        @if(Request::is('users') || Request::is('logs'))
            <script>
                setInterval(function(){
                    if($('#loading').is(':hidden') && standby == false){
                        $.ajax({
                            async: false,
                            url: "/notif_update",
                            data:{
                                checking: 'default'
                            },
                            success: function(data){
                                if(data.user_update != $('#current_updated_at').val()){
                                    $('#current_updated_at').val(data.user_update);
                                    window.location.reload();
                                }
                                if($(location).attr('pathname') == '/users'){
                                    if(data.acc_update != data_update){
                                        data_update = data.acc_update;
                                        table.ajax.reload(null, false);
                                    }
                                }
                                if($(location).attr('pathname') == '/logs'){
                                    if(data.log_update != data_update){
                                        data_update = data.log_update;
                                        table.ajax.reload(null, false);
                                    }
                                }
                            }
                        });
                    }
                }, 1100);
            </script>
        @endif
        <script>
            setInterval(() => {
                if(parseInt($('#si_notif').text()) == 0){
                    $('#si_notif').addClass('d-none')
                }
                else{
                    $('#si_notif').removeClass('d-none');
                }

                if(parseInt($('#cr_notif').text()) == 0){
                    $('#cr_notif').addClass('d-none')
                }
                else{
                    $('#cr_notif').removeClass('d-none');
                }

                if(parseInt($('#bs_notif').text()) == 0){
                    $('#bs_notif').addClass('d-none')
                }
                else{
                    $('#bs_notif').removeClass('d-none');
                }

                if(parseInt($('#or_notif').text()) == 0){
                    $('#or_notif').addClass('d-none')
                }
                else{
                    $('#or_notif').removeClass('d-none');
                }

                if(parseInt($('#dr_notif').text()) == 0){
                    $('#dr_notif').addClass('d-none')
                }
                else{
                    $('#dr_notif').removeClass('d-none');
                }
            }, 0);
        </script>
    @endif
    <main class="container-fluid content">
        @yield('content')
    </main>
    @vite('resources/js/app.js')
    @include('modals.modalViewFile')
    @include('cdn.body')
</body>
</html>