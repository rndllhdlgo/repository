
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
            $.ajax({
                url: "/edit",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    entry_id: $('#entry_id').val(),
                    current_page: $('#current_page').val(),
                    client_name: $('#client_name').val(),
                    branch_name: $('#branch_name').val(),
                },
                success: function(data){
                    if(data == 'true'){
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