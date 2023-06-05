@if(!Auth::guest())
    <script>window.location.href = '/logout'</script>
@endif
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">CREATE / RESET PASSWORD</div>
                <div class="card-body">
                    <div class="alert alert-info mb-4" role="alert" style="word-wrap: break-word !important;">
                        <div class="text-wrap"><b>The password reset link will expire after 1 hour.</b><br>If so, kindly click the <b>Forgot Your Password?</b> button in the Login page to send a new password reset link.</div>
                    </div>
                    <form method="POST" action="/password/reset">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="email" type="email" class="forminput form-control bg-white @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder=" " readonly>
                                <label for="email" class="formlabel form-label">{{ __('E-Mail Address') }}</label>
                            </div>
                            @error('email')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <b class="text-wrap">{{ $message }}</b>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="password" type="password" class="requiredField forminput form-control @error('password') is-invalid @enderror" name="password" maxlength="30" required autocomplete="new-password" placeholder=" " onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" autofocus>
                                <label for="password" class="formlabel form-label">{{ __('Enter New Password') }}</label>
                            </div>
                            @error('password')
                                <span role="alert" style="zoom: 80%; color: red;">
                                    <b>{{ $message }}</b>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="f-outline">
                                <input id="password-confirm" type="password" class="requiredField forminput form-control" name="password_confirmation" maxlength="30" required autocomplete="new-password" placeholder=" " onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false">
                                <p id="password_match" class="validation"><i class="fas fa-exclamation-triangle"></i> Password does not match!</p>
                                <label for="password-confirm" class="formlabel form-label">{{ __('Re-Enter New Password') }}</label>
                            </div>
                        </div>
                        <div class="mb-3 ml-3 text-default" style="cursor:pointer;">
                            <input type="checkbox" id="show_password" style="display:none">
                            <i class="fa-solid fa-eye fa-lg" id="show_password_eye"></i>
                            <b id="show_password_text" style="font-size:14px;">SHOW PASSWORD</b>
                        </div>
                        <div class="mb-4 ml-3">
                            <b><span class="text-default">Example Format: 1De@s3rV<br></span></b>
                            <b><span id="validation1" class="text-default"><i id="validicon1" class="fa-solid fa-circle-xmark mr-2"></i>Must be atleast 8 characters!<br></span></b>
                            <b><span id="validation2" class="text-default"><i id="validicon2" class="fa-solid fa-circle-xmark mr-2"></i>Must contain atleast 1 number!<br></span></b>
                            <b><span id="validation3" class="text-default"><i id="validicon3" class="fa-solid fa-circle-xmark mr-2"></i>Must contain atleast 1 uppercase letter!<br></span></b>
                            <b><span id="validation4" class="text-default"><i id="validicon4" class="fa-solid fa-circle-xmark mr-2"></i>Must contain atleast 1 lowercase letter!<br></span></b>
                            <b><span id="validation5" class="text-default"><i id="validicon5" class="fa-solid fa-circle-xmark mr-2"></i>Must contain atleast 1 special character!<br></span></b>
                        </div>
                        <button type="submit" class="btn btn-primary bp float-end btnRequired">
                            {{ __('Create / Reset Password') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var checkRequired = true, checkChanges = true;
setInterval(checkRequiredFields, 0);
function checkRequiredFields(){
    if($('#loading').is(':hidden')){
        if($(".optionalField:visible").length > 0){
            $('.optionalField').each(function(){
                if(!$.trim($(this).val()) && !$(this).is(':focus')){
                    $(this).val('N/A');
                }
            });
        }
        if($(".requiredField:visible").length > 0){
            $('.requiredField').each(function(){
                if(!$.trim($(this).val())){
                    $(this).addClass('requiredInput');
                }
                else{
                    $(this).removeClass('requiredInput');
                }
            });
        }
        if($(".requiredInput:visible").length > 0 || $(".requiredAlert:visible").length > 0){
            checkRequired = false;
            $('.requiredNote').show();
        }
        else{
            checkRequired = true;
            $('.requiredNote').hide();
        }
        if($(".changesNote:visible").length > 0){
            checkChanges = false;
        }
        else{
            checkChanges = true;
        }
        if(checkRequired == true && checkChanges == true && $(".invalidInput:visible").length == 0){
            $('.btnRequired').prop('disabled', false);
        }
        else{
            $('.btnRequired').prop('disabled', true);
        }
    }
}

$(document).on('focusout', '.requiredField', function(){
    if(!$(this).val()){
        alert = 'className'+$(this).attr('id');
        if($('.'+alert+':visible').length == 0){
            $(this).after('<span class="'+alert+' req"><div style="height: 18px !important;">&nbsp;</div><p class="requiredValidation"><i class="fas fa-exclamation-triangle"></i> Required Field</p></span>');
        }
    }
    else{
        alert = 'className'+$(this).attr('id');
        $('.'+alert).remove();
    }
});

$(document).ready(function(){
    $('#show_password_eye').click(function(){
        $('#show_password').click();
        if($('#show_password').is(':checked')){
            $('#show_password_text').text('HIDE PASSWORD');
            $('#show_password_eye').removeClass('fa-eye').addClass('fa-eye-slash');
            $('#password').attr('type', 'search');
            $('#password-confirm').attr('type', 'search');
        }
        else{
            $('#show_password_text').text('SHOW PASSWORD');
            $('#show_password_eye').addClass('fa-eye').removeClass('fa-eye-slash');
            $('#password').attr('type', 'password');
            $('#password-confirm').attr('type', 'password');
        }
    });
    $('#show_password_text').click(function(){
        $('#show_password_eye').click();
    });
});

setInterval(() => {
    if($('#password').val().length < 8){
        if(!$('#validation1').hasClass('text-default')){
            $('#validation1').addClass('text-default');
        }
        $('#validation1').removeClass('text-success');

        if(!$('#validicon1').hasClass('fa-circle-xmark')){
            $('#validicon1').addClass('fa-circle-xmark');
        }
        $('#validicon1').removeClass('fa-circle-check');
    }
    else{
        if(!$('#validation1').hasClass('text-success')){
            $('#validation1').addClass('text-success');
        }
        $('#validation1').removeClass('text-default');

        if(!$('#validicon1').hasClass('fa-circle-check')){
            $('#validicon1').addClass('fa-circle-check');
        }
        $('#validicon1').removeClass('fa-circle-xmark');
    }

    if(/\d/.test($('#password').val()) != true){
        if(!$('#validation2').hasClass('text-default')){
            $('#validation2').addClass('text-default');
        }
        $('#validation2').removeClass('text-success');

        if(!$('#validicon2').hasClass('fa-circle-xmark')){
            $('#validicon2').addClass('fa-circle-xmark');
        }
        $('#validicon2').removeClass('fa-circle-check');
    }
    else{
        if(!$('#validation2').hasClass('text-success')){
            $('#validation2').addClass('text-success');
        }
        $('#validation2').removeClass('text-default');

        if(!$('#validicon2').hasClass('fa-circle-check')){
            $('#validicon2').addClass('fa-circle-check');
        }
        $('#validicon2').removeClass('fa-circle-xmark');
    }

    if(/[A-Z]/.test($('#password').val()) != true){
        if(!$('#validation3').hasClass('text-default')){
            $('#validation3').addClass('text-default');
        }
        $('#validation3').removeClass('text-success');

        if(!$('#validicon3').hasClass('fa-circle-xmark')){
            $('#validicon3').addClass('fa-circle-xmark');
        }
        $('#validicon3').removeClass('fa-circle-check');
    }
    else{
        if(!$('#validation3').hasClass('text-success')){
            $('#validation3').addClass('text-success');
        }
        $('#validation3').removeClass('text-default');

        if(!$('#validicon3').hasClass('fa-circle-check')){
            $('#validicon3').addClass('fa-circle-check');
        }
        $('#validicon3').removeClass('fa-circle-xmark');
    }

    if(/[a-z]/.test($('#password').val()) != true){
        if(!$('#validation4').hasClass('text-default')){
            $('#validation4').addClass('text-default');
        }
        $('#validation4').removeClass('text-success');

        if(!$('#validicon4').hasClass('fa-circle-xmark')){
            $('#validicon4').addClass('fa-circle-xmark');
        }
        $('#validicon4').removeClass('fa-circle-check');
    }
    else{
        if(!$('#validation4').hasClass('text-success')){
            $('#validation4').addClass('text-success');
        }
        $('#validation4').removeClass('text-default');

        if(!$('#validicon4').hasClass('fa-circle-check')){
            $('#validicon4').addClass('fa-circle-check');
        }
        $('#validicon4').removeClass('fa-circle-xmark');
    }

    if(/[!@#$%^&*(),.?":{}|<>]/.test($('#password').val()) != true){
        if(!$('#validation5').hasClass('text-default')){
            $('#validation5').addClass('text-default');
        }
        $('#validation5').removeClass('text-success');

        if(!$('#validicon5').hasClass('fa-circle-xmark')){
            $('#validicon5').addClass('fa-circle-xmark');
        }
        $('#validicon5').removeClass('fa-circle-check');
    }
    else{
        if(!$('#validation5').hasClass('text-success')){
            $('#validation5').addClass('text-success');
        }
        $('#validation5').removeClass('text-default');

        if(!$('#validicon5').hasClass('fa-circle-check')){
            $('#validicon5').addClass('fa-circle-check');
        }
        $('#validicon5').removeClass('fa-circle-xmark');
    }

    if($('.fa-circle-xmark').is(':visible')){
        if(!$('#password').hasClass('invalidInput')){
            $('#password').addClass('invalidInput');
        }
        $('#password').removeClass('defaultInput');
    }
    else{
        if(!$('#password').hasClass('defaultInput')){
            $('#password').addClass('defaultInput');
        }
        $('#password').removeClass('invalidInput');
    }
}, 0);

$('#password-confirm').on('keyup',function(){
    if($('#password').val() != $('#password-confirm').val()){
        $('#password_match').show();
        if(!$('#password-confirm').hasClass('invalidInput')){
            $('#password-confirm').addClass('invalidInput');
        }
        $('#password-confirm').removeClass('defaultInput');
    }
    else{
        $('#password_match').hide();
        if(!$('#password-confirm').hasClass('defaultInput')){
            $('#password-confirm').addClass('defaultInput');
        }
        $('#password-confirm').removeClass('invalidInput');
    }
});
</script>
@endsection