var table;
$(document).ready(function(){
    $('table.userTable').dataTable().fnDestroy();
    table = $('table.userTable').DataTable({
        dom: 'ltrip',
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ Users",
            lengthMenu: "Show _MENU_ Users",
            emptyTable: "NO DATA AVAILABLE",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/users/data',
        },
        columns: [
            { data: 'user_name' },
            { data: 'user_email' },
            { data: 'department' },
            { data: 'role_name' },
            {
                data: 'user_status',
                "render": function(data, type, row, meta){
                    if(type === "sort" || type === 'type'){
                        return data;
                    }
                    if(current_department != 'ADMIN' && row.role == '1'){
                        if(data == 'ACTIVE'){
                            return `<div style="width: 120px !important;"><center class="text-success"><b>${data}</b></center></div>`;
                        }
                        if(data == 'INACTIVE'){
                            return `<div style="width: 120px !important;"><center class="text-danger"><b>${data}</b></center></div>`;
                        }
                    }
                    else{
                        if(data == 'ACTIVE'){
                            return '<center><label class="switch" style="zoom: 80%; margin-top: -5px; margin-bottom: -10px;"><input type="checkbox" class="togBtn" id="'+ meta.row +'" checked><div class="slider round"><span style="font-size: 110%;" class="on">ACTIVE</span><span style="font-size: 100%;" class="off">INACTIVE</span></div></label></center>';
                        }
                        if(data == 'INACTIVE'){
                            return '<center><label class="switch" style="zoom: 80%; margin-top: -5px; margin-bottom: -10px;"><input type="checkbox" class="togBtn" id="'+ meta.row +'"><div class="slider round"><span style="font-size: 110%;" class="on">ACTIVE</span><span style="font-size: 100%;" class="off">INACTIVE</span></div></label></center>';
                        }
                    }
                }
            },
        ],
        order: [],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            $('#loading').hide();
        }
    });

    $('.filter-select').on('change', function(){
        table.column($(this).data('column')).search(!$(this).val()?'':'^'+$(this).val()+'$',true,false,true).draw();
    });

    $('.filter-type').on('change', function(){
        $('#filter-type').val($(this).val());
        $('#filter-type').keyup();
    });

    $('.filter-type2').on('change', function(){
        $('#filter-type2').val($(this).val());
        $('#filter-type2').keyup();
    });

    $('.filter-input').on('keyup search', function(){
        table.column($(this).data('column')).search($(this).val()).draw();
    });

    setInterval(function(){
        if($('#loading').is(':hidden') && standby == false){
            $.ajax({
                url: "/users/reload",
                success: function(data){
                    if(data != data_update){
                        data_update = data;
                        table.ajax.reload(null, false);
                    }
                }
            });
        }
    }, 1000);

    $(document).on('change', '.togBtn', function(){
        var id = $(this).attr("id");
        var data = table.row(id).data();
        if($(this).is(':checked')){
            var status = 'ACTIVE';
        }
        else{
            var status = 'INACTIVE';
        }
        $.ajax({
            url: '/users/status',
            data:{
                id: data.user_id,
                name: data.user_name,
                status: status
            }
        });
    });
});

$('#btnAddUser').on('click',function(){
    $('#modal_title').html('ADD USER');
    btnAddUser();
    $('#btnSave').show();
    $('#btnUpdate').hide();
    $('#modalUser').modal('show');
});

$('#btnClear').on('click', function(){
    btnAddUser();
});

function btnAddUser(){
    $('.req').hide();
    $('#name').val('');
    $('#department').val('');
    $('#email').val('');
    $('#role').val('');
    $('.classRole').show();
}

$('#department').on('change',function(){
    if($('#department').val() == 'BOSS'){
        $('.classRole').hide();
        $('#optionBoss').show();
        $('#role').val('4');
    }
    else{
        $('.classRole').show();
        $('#optionBoss').hide();
        $('#role').val('');
    }
});

$(document).on('click', '#userTable tbody tr td:not(:nth-child(5))', function(){
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();
    if(current_department != 'ADMIN' && data.role == '1'){
        return false;
    }
    $('.req').hide();
    $('#user_id').val(data.user_id);
    $('#name').val(data.user_name);
    $('#department').val(data.department);
    $('#email').val(data.user_email);
    $('#role').val(data.role);

    if($('#department').val() == 'BOSS'){
        $('.classRole').hide();
        $('#optionBoss').show();
        $('#role').val('4');
    }
    else{
        $('.classRole').show();
        $('#optionBoss').hide();
    }

    $('#modal_title').html('UPDATE USER');
    $('#btnSave').hide();
    $('#btnUpdate').show();
    $('#modalUser').modal('show');
});

$('#btnSave').on('click',function(){
    var name = $.trim($('#name').val());
    var department = $.trim($('#department').val());
    var email = $.trim($('#email').val());
    var role = $('#role').val();

    if(validateEmail(email) == false){
        Swal.fire('INVALID EMAIL', 'Please enter valid email format.', 'error');
        return false;
    }
    $.ajax({
        url: "/users/validate/save",
        type: "POST",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            email: email
        },
        success: function(data){
            if(data.result == 'true'){
                Swal.fire({
                    title: "ADD NEW USER?",
                    html: "You are about to ADD a new user!",
                    icon: "warning",
                    showCancelButton: true,
                    cancelButtonColor: '#3085d6',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Confirm',
                    allowOutsideClick: false
                })
                .then((result) => {
                    if(result.isConfirmed){
                        $('#modalUser').modal('hide');
                        $('#loading').show();
                        $.ajax({
                            url: "/users/save",
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                name: name,
                                email: email,
                                role: role,
                                department: department,
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#loading').hide();
                                    Swal.fire("SAVE SUCCESS", "New user saved successfully!", "success");
                                    setTimeout(function(){window.location.href="/users"}, 2000);
                                }
                                else{
                                    $('#loading').hide();
                                    Swal.fire("SAVE FAILED", "New user save failed!", "error");
                                }
                            }
                        });
                    }
                });
            }
            else if(data.result == 'duplicate'){
                Swal.fire("DUPLICATE EMAIL", "Email address already exists!", "error");
                return false;
            }
            else{
                $('#addUser').hide();
                Swal.fire("SAVE FAILED", "USER ACCOUNT", "error");
                setTimeout(function(){window.location.href="/users"}, 2000);
            }
        }
    });
});

$('#btnUpdate').on('click',function(){
    var user_id = $('#user_id').val();
    var name = $.trim($('#name').val());
    var department = $.trim($('#department').val());
    var email = $.trim($('#email').val());
    var role = $('#role').val();

    if(validateEmail(email) == false){
        Swal.fire('INVALID EMAIL', 'Please enter valid email format.', 'error');
        return false;
    }
    $.ajax({
        url: "/users/validate/update",
        type: "PUT",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:{
            user_id: user_id,
            email: email
        },
        success: function(data){
            if(data == 'true'){
                Swal.fire({
                    title: "UPDATE USER DETAILS?",
                    html: "You are about to UPDATE this user!",
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
                            url: "/users/update",
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data:{
                                user_id: user_id,
                                name: name,
                                department: department,
                                email: email,
                                role: role,
                            },
                            success: function(data){
                                if(data == 'true'){
                                    $('#modalUser').modal('hide');
                                    Swal.fire("UPDATE SUCCESS", "User details updated successfully!", "success");
                                    setTimeout(function(){window.location.href="/users"}, 2000);
                                }
                                else if(data == 'no changes'){
                                    $('#loading').hide();
                                    Swal.fire("NO CHANGES FOUND", "User Details are all still the same!", "warning");
                                }
                                else{
                                    Swal.fire("UPDATE FAILED", "User details update failed!", "error");
                                }
                            }
                        });
                    }
                });
            }
            else if(data == 'duplicate'){
                Swal.fire("DUPLICATE EMAIL", "Email address already exists!", "error");
            }
            else{
                $('#updateUser').hide();
                Swal.fire("UPDATE FAILED", "USER ACCOUNT", "error");
                setTimeout(function(){window.location.href="/users"}, 2000);
            }
        }
    });
});

setInterval(() => {
    if(current_department != 'ADMIN'){
        $('.classDepartment').hide();
        $('.removeOption').remove();
        $('#department').val(current_department);
    }
}, 0);