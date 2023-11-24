<div class="modal fade in" id="modalUser">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header bg-default text-center" style="height: 45px; border-radius: 0px;">
            <h6 class="modal-title w-100" id="modal_title"></h6>
            <button type="button" style="zoom: 80%;" class="btn-close btn-close-white close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="background-color: white; color: black;">
            <form>
                <input type="hidden" id="user_id">
                <div class="mb-4">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="name" name="name" placeholder=" ">
                        <label for="name" class="formlabel form-label">FULL NAME</label>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="f-outline">
                        <input class="forminput form-control requiredField bg-white text-lowercase" type="search" id="email" name="email" placeholder=" ">
                        <label for="email" class="formlabel form-label">EMAIL ADDRESS</label>
                    </div>
                </div>
                <div class="mb-4 classCompany">
                    <div class="f-outline">
                        <select class="forminput form-control form-select requiredField multiple_field bg-white" id="company" name="company" data-placeholder="Select Company Name/s" multiple>
                            <option value="1" style="color: Black;">APSOFT</option>
                            <option value="2" style="color: Black;">IDSI</option>
                            <option value="3" style="color: Black;">PLSI</option>
                        </select>
                        <label for="company" class="formlabel form-label">COMPANY</label>
                    </div>
                </div>
                <div class="mb-4 classDepartment">
                    <div class="f-outline">
                        <select class="forminput form-control form-select requiredField bg-white" id="department" name="department">
                            <option value="" selected disabled style="color: Gray;">SELECT DEPARTMENT</option>
                            @if(auth()->user()->department == 'SUPERUSER')
                                <option value="SUPERUSER" style="color: Black;">SUPERUSER</option>
                            @endif
                            @if(auth()->user()->department == 'SUPERUSER' || auth()->user()->department == 'ACCOUNTING')
                                <option value="ACCOUNTING" style="color: Black;">ACCOUNTING</option>
                            @endif
                            @if(auth()->user()->department == 'SUPERUSER' || auth()->user()->department == 'WAREHOUSE')
                                <option value="WAREHOUSE" style="color: Black;">WAREHOUSE</option>
                            @endif
                            @if(auth()->user()->department == 'SUPERUSER' || auth()->user()->department == 'SALES')
                                <option value="SALES" style="color: Black;">SALES</option>
                            @endif
                        </select>
                        <label for="department" class="formlabel form-label">DEPARTMENT</label>
                    </div>
                </div>
                <div class="mb-4 classRole">
                    <div class="f-outline">
                        <select class="forminput form-control form-select requiredField bg-white" id="role" name="role">
                            <option value="" selected disabled style="color: Gray;">SELECT USER LEVEL</option>
                            <option value="1" style="color: Black;" class="admin">ADMIN</option>
                            <option value="2" style="color: Black;" class="encoder">ENCODER</option>
                            <option value="3" style="color: Black;" class="viewer">VIEWER</option>
                            <option value="4" style="color: Black;" class="boss">BOSS</option>
                        </select>
                        <label for="role" class="formlabel form-label">USER LEVEL</label>
                    </div>
                </div>
                <div class="mt-4" style="zoom: 85%;">
                    <button type="button" id="btnClear" class="btn btn-outline-danger" onclick="$('#name').focus();"><i class="fas fa-eraser"></i> CLEAR</button>
                    <button type="button" id="btnSave" class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> SAVE</button>
                    <button type="button" id="btnUpdate" class="btn btn-primary float-end bp btnRequired" style="display:none;"><i class="fas fa-save"></i> UPDATE</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>