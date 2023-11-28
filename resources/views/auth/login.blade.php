@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mt-4">
                <div style="height: 30px;" class="card-header">
                    <i class="fa fa-user-circle fa-4x rounded-circle text-default" style="margin-top: -30px; padding: 1px; background-color: ghostwhite;"></i>
                </div>
                <div class="card-body">
                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="email" type="search" class="forminput form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" " autofocus>
                                <label for="email" class="formlabel form-label">{{ __('E-Mail Address') }}</label>
                            </div>
                            @error('email')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <b>{{ $message }}</b>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="password" type="password" class="forminput form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder=" ">
                                <label for="password" class="formlabel form-label">{{ __('Password') }}</label>
                            </div>
                            @error('password')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <b>{{ $message }}</b>
                                </span>
                            @enderror
                        </div>
                        <div class="row mb-0">
                            <div class="mb-3 ml-3 text-default" style="cursor:pointer;">
                                <input type="checkbox" id="show_password" style="display:none">
                                <i class="fa-solid fa-eye fa-lg" id="show_password_eye"></i>
                                <b id="show_password_text" style="font-size:14px;">SHOW PASSWORD</b>
                            </div>
                            <div class="col-md-12">
                                <button id="btnLogin" type="submit" class="btn btn-primary btnLogin bp">
                                    {{ __('LOGIN') }}<i class="fa fa-sign-in ml-2" aria-hidden="true"></i>
                                </button>
                                @if (Route::has('password.request'))
                                    <span id="btnForgotPassword" class="btn btn-link ml-2" style="font-weight: bold; cursor: pointer;">
                                        {{ __('Forgot Your Password?') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@if(env('MAIL_ENABLED') == 'N')
    <script>
        $('#btnForgotPassword').on('click', function(){
            Swal.fire({
                title: "EMAIL SERVER UNAVAILABLE",
                html: "Email server is temporarily down. <br>Please contact administrator.",
                icon: "error"
            });
        });
    </script>
@else
    <script>
        $('#btnForgotPassword').on('click', function(){
            window.location.href = "{{ route('password.request') }}";
        });
    </script>
@endif
<script>
    $(document).ready(function(){
        if($(location).attr('pathname')+window.location.search == '/login?user=inactive'){
            Swal.fire({
                title: "INACTIVE ACCOUNT",
                html: "Your account is currently inactive. Please contact the administrator to resolve the issue.",
                icon: "warning",
                allowOutsideClick: false
            })
            .then((result) => {
                if(result.isConfirmed){
                    window.location.href = "/login";
                }
            });
        }
        else if ($(location).attr('pathname')+window.location.search == '/login?user=session'){
            if(!document.referrer.includes('/password/reset')){
                Swal.fire({
                    title: "SESSION CONFLICT",
                    html: "<strong>Warning:</strong> You are being kicked out because someone else is logging in.",
                    icon: "warning",
                    allowOutsideClick: false
                })
                .then((result) => {
                    if(result.isConfirmed){
                        window.location.href = "/login";
                    }
                });
            }
        }
    });
    $(document).ready(function(){
        $('#show_password_eye').click(function(){
            $('#show_password').click();
            if($('#show_password').is(':checked')){
                $('#show_password_text').text('HIDE PASSWORD');
                $('#show_password_eye').removeClass('fa-eye').addClass('fa-eye-slash');
                $('#password').attr('type', 'search');
            }
            else{
                $('#show_password_text').text('SHOW PASSWORD');
                $('#show_password_eye').addClass('fa-eye').removeClass('fa-eye-slash');
                $('#password').attr('type', 'password');
            }
        });
        $('#show_password_text').click(function(){
            $('#show_password_eye').click();
        });
    });
</script>
@endsection