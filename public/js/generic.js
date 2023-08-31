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
                            sales_invoice: $('#sales_invoice').val(),
                            collection_receipt: $('#collection_receipt').val(),
                            official_receipt: $('#official_receipt').val(),
                            delivery_receipt: $('#delivery_receipt').val(),
                            company: $('#company').val(),
                            client_name: $('#client_name').val(),
                            branch_name: $('#branch_name').val(),
                            date_created: $('#date_created').val(),
                            date_received: $('#date_received').val(),
                            sales_order: $('#sales_order').val(),
                            purchase_order: $('#purchase_order').val(),
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
                        sales_invoice: $('#sales_invoice').val(),
                        collection_receipt: $('#collection_receipt').val(),
                        official_receipt: $('#official_receipt').val(),
                        delivery_receipt: $('#delivery_receipt').val(),
                        company: $('#company').val(),
                        client_name: $('#client_name').val(),
                        branch_name: $('#branch_name').val(),
                        date_created: $('#date_created').val(),
                        date_received: $('#date_received').val(),
                        sales_order: $('#sales_order').val(),
                        purchase_order: $('#purchase_order').val(),
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

function edit_pdf(){
    var formData = new FormData();
    if($('#current_page').val() == 'bs'){
        var entry_id = $('#entry_id').val();
        var billing_statement = $('#billing_statement').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var date_created = $('#date_created').val();
        var sales_order = $('#sales_order').val();
        var purchase_order = $('#purchase_order').val();
        var pdf_file = $('#pdf_file').prop('files')[0];

        formData.append('entry_id', entry_id);
        formData.append('billing_statement', billing_statement);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('date_created', date_created);
        formData.append('sales_order', sales_order);
        formData.append('purchase_order', purchase_order);
        formData.append('pdf_file', pdf_file);

        var url_name = '/edit_bs';
    }
    else if($('#current_page').val() == 'si'){
        var entry_id = $('#entry_id').val();
        var sales_invoice = $('#sales_invoice').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var date_created = $('#date_created').val();
        var date_received = $('#date_received').val();
        var purchase_order = $('#purchase_order').val();
        var sales_order = $('#sales_order').val();
        var delivery_receipt = $('#delivery_receipt').val();
        var pdf_file = $('#pdf_file').prop('files')[0];

        formData.append('entry_id', entry_id);
        formData.append('sales_invoice', sales_invoice);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('date_created', date_created);
        formData.append('date_received', date_received);
        formData.append('purchase_order', purchase_order);
        formData.append('sales_order', sales_order);
        formData.append('delivery_receipt', delivery_receipt);
        formData.append('pdf_file', pdf_file);

        var url_name = '/edit_si';
    }
    else if($('#current_page').val() == 'cr'){
        var entry_id = $('#entry_id').val();
        var collection_receipt = $('#collection_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var date_created = $('#date_created').val();
        var sales_order = $('#sales_order').val();
        var sales_invoice = $('#sales_invoice').val();
        var pdf_file = $('#pdf_file').prop('files')[0];

        formData.append('entry_id', entry_id);
        formData.append('collection_receipt', collection_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('date_created', date_created);
        formData.append('sales_order', sales_order);
        formData.append('sales_invoice', sales_invoice);
        formData.append('pdf_file', pdf_file);

        var url_name = '/edit_cr';
    }
    else if($('#current_page').val() == 'or'){
        var entry_id = $('#entry_id').val();
        var official_receipt = $('#official_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var date_created = $('#date_created').val();
        var sales_order = $('#sales_order').val();
        var pdf_file = $('#pdf_file').prop('files')[0];

        formData.append('entry_id', entry_id);
        formData.append('official_receipt', official_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('date_created', date_created);
        formData.append('sales_order', sales_order);
        formData.append('pdf_file', pdf_file);

        var url_name = '/edit_or';
    }
    else if($('#current_page').val() == 'dr'){
        var entry_id = $('#entry_id').val();
        var delivery_receipt = $('#delivery_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var date_created = $('#date_created').val();
        var date_received = $('#date_received').val();
        var purchase_order = $('#purchase_order').val();
        var sales_order = $('#sales_order').val();
        var pdf_file = $('#pdf_file').prop('files')[0];

        formData.append('entry_id', entry_id);
        formData.append('delivery_receipt', delivery_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('date_created', date_created);
        formData.append('date_received', date_received);
        formData.append('purchase_order', purchase_order);
        formData.append('sales_order', sales_order);
        formData.append('pdf_file', pdf_file);

        var url_name = '/edit_dr';
    }
    else{
        return false;
    }

    $.ajax({
        url: url_name,
        method: 'post',
        data: formData,
        contentType : false,
        processData : false,
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
            $('#loading').hide();
            if(response == 'no changes'){
                $('#loading').hide();
                Swal.fire("NO CHANGES FOUND", "", "error");
            }
            else if(response == 'invalid'){
                Swal.fire({
                    title: 'SAVE SUCCESS',
                    html: "FILE UPLOADED SUCCESSFULLY BUT NOT VALIDATED",
                    icon: 'warning'
                });
                $('.modal').modal('hide');
            }
            else if(response == 'Invalid file format'){
                Swal.fire({
                    title: 'EDIT FAILED',
                    html: "INVALID FILE FORMAT",
                    icon: 'error',
                });
            }
            else{
                Swal.fire({
                    title: 'SAVE SUCCESS',
                    html: 'FILE SUCCESSFULLY CREATED',
                    icon: 'success'
                });
                $('.modal').modal('hide');
            }
        }
    });
}

$(document).on('click','#btnApprove', function(){
    if(current_role == 'ADMIN'){
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
    }
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

setInterval(() => {
    if(current_role == 'BOSS'){
        $('.forminput').prop('disabled',true);
    }
}, 0);

$(document).on('click', '#btnViewFile', function(){
    $('#displayFile').empty().append(`
        <embed src="${$('#fetchFileName').attr('href')}" width="100%" height="600px"/>
    `)
    $('#modalViewFile').modal('show');
});

$(document).on('contextmenu', '.preventRightClick', function(e) {
    e.preventDefault();
});