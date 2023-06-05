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