<div class="modal fade in" id="changePassword">
    <div class="modal-dialog modal-m modal-dialog-centered">
    <div class="modal-content card">
        <div class="modal-header text-center bg-default" style="border-radius: 0px; height: 45px;">
            <h6 class="modal-title w-100">CHANGE PASSWORD</h6>
            <button type="button" style="zoom: 80%;" class="btn-close btn-close-white close closePassword" data-bs-dismiss="modal" data-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <div class="alert alert-primary requiredNote p-2" role="alert" style="display: none;">
                <i class='fa fa-exclamation-triangle'></i>
                <b>NOTE:</b> Please fill up all fields.
            </div>
            <form id="form_changepassword">
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField" type="password" id="pass1" name="pass1" minlength="8" maxlength="30" placeholder=" " onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" required autofocus>
                        <label class="formlabel form-label" for="pass1">Enter Current Password</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField" type="password" id="pass2" name="pass2" minlength="8" maxlength="30" placeholder=" " onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" required>
                        <label class="formlabel form-label" for="pass2">Enter New Password</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField" type="password" id="pass3" name="pass3" minlength="8" maxlength="30" placeholder=" " onselectstart="return false" onpaste="return false" oncopy="return false" oncut="return false" ondrag="return false" ondrop="return false" required>
                        <p id="password_match" class="validation"><i class="fas fa-exclamation-triangle"></i> Password does not match!</p>
                        <label class="formlabel form-label" for="pass3">Re-Enter New Password</label>
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
                <div style="zoom: 85%;">
                    <button type="reset" id="btnResetChange" class="btn btn-outline-danger" onclick="$('#pass1').focus();"><i class="fas fa-eraser"></i> CLEAR</button>
                    <button type="button" id="btnChangePassword" class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> UPDATE</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<script>
setInterval(() => {
    if($('#changePassword').is(':visible')){
        if($('#pass2').val().length < 8){
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

        if(/\d/.test($('#pass2').val()) != true){
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

        if(/[A-Z]/.test($('#pass2').val()) != true){
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

        if(/[a-z]/.test($('#pass2').val()) != true){
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

        if(/[!@#$%^&*(),.?":{}|<>]/.test($('#pass2').val()) != true){
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
            if(!$('#pass2').hasClass('invalidInput')){
                $('#pass2').addClass('invalidInput');
            }
            $('#pass2').removeClass('defaultInput');
        }
        else{
            if(!$('#pass2').hasClass('defaultInput')){
                $('#pass2').addClass('defaultInput');
            }
            $('#pass2').removeClass('invalidInput');
        }
    }
}, 0);

$('#pass3').on('keyup',function(){
    if($('#pass2').val() != $('#pass3').val()){
        $('#password_match').show();
        if(!$('#pass3').hasClass('invalidInput')){
            $('#pass3').addClass('invalidInput');
        }
        $('#pass3').removeClass('defaultInput');
    }
    else{
        $('#password_match').hide();
        if(!$('#pass3').hasClass('defaultInput')){
            $('#pass3').addClass('defaultInput');
        }
        $('#pass3').removeClass('invalidInput');
    }
});

$(document).ready(function(){
    $('#show_password_eye').click(function(){
        $('#show_password').click();
        if($('#show_password').is(':checked')){
            $('#show_password_text').text('HIDE PASSWORD');
            $('#show_password_eye').removeClass('fa-eye').addClass('fa-eye-slash');
            $('#pass1').attr('type', 'search');
            $('#pass2').attr('type', 'search');
            $('#pass3').attr('type', 'search');
        }
        else{
            $('#show_password_text').text('SHOW PASSWORD');
            $('#show_password_eye').addClass('fa-eye').removeClass('fa-eye-slash');
            $('#pass1').attr('type', 'password');
            $('#pass2').attr('type', 'password');
            $('#pass3').attr('type', 'password');
        }
    });
    $('#show_password_text').click(function(){
        $('#show_password_eye').click();
    });
});

$(document).on('click', '#lblChangePassword', function(){
    $('#pass1').val('');
    $('#pass2').val('');
    $('#pass3').val('');
    $('#changePassword').modal('show');
});

$('#btnChangePassword').on('click', function(){
    var pass1 = $('#pass1').val();
    var pass2 = $('#pass2').val();
    var pass3 = $('#pass3').val();
    if(pass1 == "" || pass2 == "" || pass3 == ""){
        $('#form_changepassword')[0].reportValidity();
        return false;
    }
    else if(pass1.length < 8 || pass2.length < 8 || pass3.length < 8){
        $('#form_changepassword')[0].reportValidity();
        return false;
    }
    else{
        if(pass2 != pass3){
            Swal.fire('NEW PASSWORD MISMATCH','The password confirmation does not match!','error');
            return false;
        }
        else{
            $.ajax({
                url: "/change/validate",
                data:{
                    current: pass1
                },
                success: function(data){
                    if(data == 'true'){
                        Swal.fire({
                            title: "CHANGE PASSWORD?",
                            html: "You are about to CHANGE your current user account password!",
                            icon: "warning",
                            showCancelButton: true,
                            cancelButtonColor: '#3085d6',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Confirm',
                            allowOutsideClick: false
                        })
                        .then((result) => {
                            if(result.isConfirmed){
                                $.ajax({
                                    url: "/change/password",
                                    data:{
                                        new: pass2
                                    },
                                    success: function(data){
                                        if(data == 'true'){
                                            $('.closePassword').click();
                                            Swal.fire("UPDATE SUCCESS", "User changed password successfully!", "success");
                                            return true;
                                        }
                                        else{
                                            Swal.fire("UPDATE FAILED", "User password change failed!", "error");
                                            return true;
                                        }
                                    }
                                });
                            }
                        });
                    }
                    else{
                        Swal.fire('INCORRECT','Incorrect Current Password!', 'error');
                        return false;
                    }
                }
            });
        }
    }
});
</script>