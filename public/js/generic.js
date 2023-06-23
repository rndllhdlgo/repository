
$(document).on('click','#btnEdit', function(){
    Swal.fire({
        title: 'Do you want to update?',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        customClass: {
        actions: 'my-actions',
        confirmButton: 'order-2',
        denyButton: 'order-3',
        }
    }).then((save) => {
        if(save.isConfirmed){
            if($('#pdf_file').length > 0){
                if($('#pdf_file').get(0).files.length > 0){
                    $('#loading').show();
                        setTimeout(() => {
                            edit_pdf();
                        }, 200);
                }
                else{
                    $('#loading').show();
                    $.ajax({
                        url: "/edit",
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{
                            entry_id: $('#entry_id').val(),
                            current_page: $('#current_page').val(),
                            billing_statement: $('#billing_statement').val(),
                            company: $('#company').val(),
                            client_name: $('#client_name').val(),
                            branch_name: $('#branch_name').val(),
                            date_created: $('#date_created').val(),
                            sales_order: $('#sales_order').val(),
                            purchase_order: $('#purchase_order').val()
                        },
                        success: function(data){
                            if(data == 'no changes'){
                                $('#loading').hide();
                                Swal.fire("NO CHANGES FOUND", "", "error");
                            }
                            else if(data == 'true'){
                                $('#loading').hide();
                                Swal.fire("UPDATE SUCCESS", "", "success");
                                $('.modal').modal('hide');
                            }
                            else{
                                $('#loading').hide();
                                Swal.fire("UPDATE FAILED", "", "error");
                            }
                        }
                    });
                }
            }
            else{
                $('#loading').show();
                $.ajax({
                    url: "/edit",
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        entry_id: $('#entry_id').val(),
                        current_page: $('#current_page').val(),
                        billing_statement: $('#billing_statement').val(),
                        company: $('#company').val(),
                        client_name: $('#client_name').val(),
                        branch_name: $('#branch_name').val(),
                        date_created: $('#date_created').val(),
                        sales_order: $('#sales_order').val(),
                        purchase_order: $('#purchase_order').val()
                    },
                    success: function(data){
                        if(data == 'no changes'){
                            $('#loading').hide();
                            Swal.fire("NO CHANGES FOUND", "", "error");
                        }
                        else if(data == 'true'){
                            $('#loading').hide();
                            Swal.fire("UPDATE SUCCESS", "", "success");
                            $('.modal').modal('hide');
                        }
                        else{
                            $('#loading').hide();
                            Swal.fire("UPDATE FAILED", "", "error");
                        }
                    }
                });
            }
        }
    });
});

$(document).on('click','#btnApprove', function(){
    Swal.fire({
        title: 'Do you want to approve?',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        customClass: {
        actions: 'my-actions',
        confirmButton: 'order-2',
        denyButton: 'order-3',
        }
    }).then((save) => {
        if(save.isConfirmed){
            $.ajax({
                url: "/approve",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    entry_id: $('#entry_id').val(),
                    current_page: $('#current_page').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire("APPROVE SUCCESS", "", "success");
                        $('.modal').modal('hide');
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire("APPROVE FAILED", "", "error");
                    }
                }
            });
        }
    });
});
setInterval(function(){
    if($('#loading').is(':hidden') && standby == false){
        $.ajax({
            url: "/table_reload",
            data:{
                current_page: $('#current_page').val(),
            },
            success: function(data){
                if(data != data_update){
                    data_update = data;
                    table.ajax.reload(null, false);
                }
            }
        });
    }
}, 1000);