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
                            business_name: $('#business_name').val(),
                            branch_name: $('#branch_name').val(),
                            sales_order: $('#sales_order').val(),
                            purchase_order: $('#purchase_order').val(),
                            uploaded_by: $('#uploaded_by').val()
                        },
                        success: function(data){
                            if(data == 'no changes'){
                                $('#loading').hide();
                                Swal.fire("NO CHANGES FOUND", "", "error");
                            }
                            else if(data == 'true'){
                                $('#loading').hide();
                                Swal.fire("UPDATE SUCCESS", "", "success");
                                if(current_role != 'ADMIN'){
                                    $('.modal').modal('hide');
                                }
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
                        business_name: $('#business_name').val(),
                        branch_name: $('#branch_name').val(),
                        sales_order: $('#sales_order').val(),
                        purchase_order: $('#purchase_order').val(),
                        uploaded_by: $('#uploaded_by').val()
                    },
                    success: function(data){
                        if(data == 'no changes'){
                            $('#loading').hide();
                            Swal.fire("NO CHANGES FOUND", "", "error");
                        }
                        else if(data == 'true'){
                            $('#loading').hide();
                            Swal.fire("UPDATE SUCCESS", "", "success");
                            if(current_role != 'ADMIN'){
                                $('.modal').modal('hide');
                            }
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
        var sales_order = $('#sales_order').val();
        var purchase_order = $('#purchase_order').val();
        var uploaded_by = $('#uploaded_by').val();
        var pdf_files = $('#pdf_file').prop('files');

        formData.append('entry_id', entry_id);
        formData.append('billing_statement', billing_statement);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('sales_order', sales_order);
        formData.append('purchase_order', purchase_order);
        formData.append('uploaded_by', uploaded_by);
        for(let i = 0; i < pdf_files.length; i++){
            formData.append('pdf_file[]', pdf_files[i]);
        }

        var url_name = '/edit_bs';
    }
    else if($('#current_page').val() == 'si'){
        var entry_id = $('#entry_id').val();
        var sales_invoice = $('#sales_invoice').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var purchase_order = $('#purchase_order').val();
        var sales_order = $('#sales_order').val();
        var delivery_receipt = $('#delivery_receipt').val();
        var uploaded_by = $('#uploaded_by').val();
        var pdf_files = $('#pdf_file').prop('files');

        formData.append('entry_id', entry_id);
        formData.append('sales_invoice', sales_invoice);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('purchase_order', purchase_order);
        formData.append('sales_order', sales_order);
        formData.append('delivery_receipt', delivery_receipt);
        formData.append('uploaded_by', uploaded_by);
        for(let i = 0; i < pdf_files.length; i++){
            formData.append('pdf_file[]', pdf_files[i]);
        }

        var url_name = '/edit_si';
    }
    else if($('#current_page').val() == 'cr'){
        var entry_id = $('#entry_id').val();
        var collection_receipt = $('#collection_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var sales_order = $('#sales_order').val();
        var sales_invoice = $('#sales_invoice').val();
        var uploaded_by = $('#uploaded_by').val();
        var pdf_files = $('#pdf_file').prop('files');

        formData.append('entry_id', entry_id);
        formData.append('collection_receipt', collection_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('sales_order', sales_order);
        formData.append('sales_invoice', sales_invoice);
        formData.append('uploaded_by', uploaded_by);
        for(let i = 0; i < pdf_files.length; i++){
            formData.append('pdf_file[]', pdf_files[i]);
        }

        var url_name = '/edit_cr';
    }
    else if($('#current_page').val() == 'or'){
        var entry_id = $('#entry_id').val();
        var official_receipt = $('#official_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var branch_name = $('#branch_name').val();
        var sales_order = $('#sales_order').val();
        var uploaded_by = $('#uploaded_by').val();
        var pdf_files = $('#pdf_file').prop('files');

        formData.append('entry_id', entry_id);
        formData.append('official_receipt', official_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('branch_name', branch_name);
        formData.append('sales_order', sales_order);
        formData.append('uploaded_by', uploaded_by);
        for(let i = 0; i < pdf_files.length; i++){
            formData.append('pdf_file[]', pdf_files[i]);
        }

        var url_name = '/edit_or';
    }
    else if($('#current_page').val() == 'dr'){
        var entry_id = $('#entry_id').val();
        var delivery_receipt = $('#delivery_receipt').val();
        var company = $('#company').val();
        var client_name = $('#client_name').val();
        var business_name = $('#business_name').val();
        var branch_name = $('#branch_name').val();
        var purchase_order = $('#purchase_order').val();
        var uploaded_by = $('#uploaded_by').val();
        var pdf_files = $('#pdf_file').prop('files');

        formData.append('entry_id', entry_id);
        formData.append('delivery_receipt', delivery_receipt);
        formData.append('company', company);
        formData.append('client_name', client_name);
        formData.append('business_name', business_name);
        formData.append('branch_name', branch_name);
        formData.append('purchase_order', purchase_order);
        formData.append('sales_order', sales_order);
        formData.append('uploaded_by', uploaded_by);
        for(let i = 0; i < pdf_files.length; i++){
            formData.append('pdf_file[]', pdf_files[i]);
        }

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
                    html: 'FILE UPLOADED SUCCESSFULLY FOR VALIDATION',
                    icon: 'success'
                });
                $('.modal').modal('hide');
            }
        }
    });
}

$(document).on('click','#btnApprove', function(){
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
                Swal.fire({
                    title: 'VALIDATE SUCCESS',
                    html: '<br>',
                    icon: 'success',
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                $('.modal').modal('hide');
            }
            else{
                $('#loading').hide();
                Swal.fire("VALIDATE FAILED", "", "error");
            }
        }
    });
});

$(document).on('click','#btnDisapprove', function(){
    Swal.fire({
        title: 'MARK AS INVALID?',
        html: `<textarea class="w-100 requiredField" rows="5" placeholder="Please leave a comment here" id="remarks"></textarea>`,
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
                url: "/disapprove",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    entry_id: $('#entry_id').val(),
                    current_page: $('#current_page').val(),
                    remarks: $('#remarks').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire("INVALIDATE SUCCESS", "", "success");
                        $('.modal').modal('hide');
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire("INVALIDATE FAILED", "", "error");
                    }
                }
            });
        }
    });
});

$(document).on('click','#btnReturn', function(){
    Swal.fire({
        title: 'RETURN TO ENCODER?',
        html: `<textarea class="w-100 requiredField" rows="5" placeholder="Please leave a comment here" id="remarks"></textarea>`,
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
                url: "/return_to_encoder",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    entry_id: $('#entry_id').val(),
                    current_page: $('#current_page').val(),
                    remarks: $('#remarks').val()
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire("RETURN SUCCESS", "", "success");
                        $('.modal').modal('hide');
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire("RETURN FAILED", "", "error");
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
                if(data.data_update != data_update){
                    data_update = data.data_update;
                    table.ajax.reload(null, false);
                }
            }
        });
    }
}, 1000);

setInterval(function(){
    if($('#loading').is(':hidden') && standby == false){
        $.ajax({
            url: "/notif_update",
            success: function(data){
                if(data.si_update != si_update){
                    si_update = data.si_update;
                    $('#si_notif').html(data.si_count);
                }
                if(data.cr_update != cr_update){
                    cr_update = data.cr_update;
                    $('#cr_notif').html(data.cr_count);
                }
                if(data.bs_update != bs_update){
                    bs_update = data.bs_update;
                    $('#bs_notif').html(data.bs_count);
                }
                if(data.or_update != or_update){
                    or_update = data.or_update;
                    $('#or_notif').html(data.or_count);
                }
                if(data.dr_update != dr_update){
                    dr_update = data.dr_update;
                    $('#dr_notif').html(data.dr_count);
                }
            }
        });
    }
}, 1000);

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

$(document).on('click', '#btnViewFile', function(){
    $('#displayFile').empty().append(`
        <embed src="${$('#fetchFileName').attr('href')}" width="100%" height="600px"/>
    `);
});

$(document).on('contextmenu', '.preventRightClick', function(e) {
    e.preventDefault();
});


$(document).on('change','#pdf_file', function(e){
    var files = e.target.files;

    var pdf_count = 0;
    var img_count = 0;
    if (!files.length) {
        return false;
    }
    $('.pItem').remove();
    $("#displayFile").empty();
    for(var i = 0; i < files.length; i++){
        var file = files[i];
        var fileType = getExtension(file.name);

        if(fileType === 'jpg' || fileType === 'jpeg'){
            img_count++;
        }
        else if(fileType === 'pdf'){
            pdf_count++;
        }
    }

    if(pdf_count > 1){
        Swal.fire({
            icon: 'warning',
            title: 'MAX LIMIT REACHED',
            html: '<span id="checkUpload">Only 1 PDF file can be uploaded.</span>'
        });

        resetUpload();
        return false;
    }
    else if(pdf_count > 0 && img_count > 0){
        Swal.fire({
            icon: 'warning',
            title: 'INVALID UPLOAD',
            html: '<span id="checkUpload">Cannot upload different file types.</span>'
        });

        resetUpload();
        return false;
    }

    for(var i = 0; i < files.length; i++){
      var file = files[i];

      var reader = new FileReader();

      reader.onload = function(e){
        var fileType = getFileType(e.target.result);

        if(fileType === 'jpg' || fileType === 'jpeg' || fileType === 'pdf'){
            if(fileType === 'pdf'){
                var embed = $("<embed style='height:470px; width:100%;'>").attr("src", e.target.result).addClass("pdf-embed");
                $("#displayFile").append(embed);
            }
            else{
                var img = $(`<img class="imgPreview" style='width:100%; display: none;'>`).attr("src", e.target.result); // Create an <img> element
                $("#displayFile").append(img);
            }
        }
      };
      $('.pagi:last').after(`<li class="page-item pagi pItem"><a class="page-link" onclick="paging(${i+1})">${i+1}</a></li>`)
      reader.readAsDataURL(file);
      if(i+1 == files.length){
        setTimeout(() => {
            paging('1');
        }, 200);
      }
    }
    $('.divReplaceFile').show();
});

$(document).on('keyup','.current_search',function(){
    $("label:contains('Search:')").find("input:first").val($(this).val());
    $("label:contains('Search:')").find("input:first").keyup();
});

function display_search(){
    $("label:contains('Search:')").addClass('d-none');
    $("label:contains('Search:')").after(`
        <div class="input-group">
            <input class="form-control current_search" type="search" placeholder="SEARCH...">
            <button class="bg-white search_bar_right" type="button">
                <i class="fa fa-search text-white"></i>&nbsp;
            </button>
        </div>
    `);
}

function paging(id){
    $('.imgPreview').hide();
    $('#imgPreview'+id).show();
}

function getFileType(dataURL){
    var mimeType = dataURL.split(',')[0].split(':')[1].split(';')[0];
    if(mimeType === 'application/pdf'){
        return 'pdf';
    }
    else if(mimeType === 'image/jpeg' || mimeType === 'image/jpg'){
        return 'jpg';
    }
    return '';
}

function getExtension(fileName){
    var fileExtension = fileName.split('.').pop().toLowerCase();
    return fileExtension;
}

function resetUpload(){
    $('.pItem').remove();

    $('#displayFile').empty().append(`
        <center id="logoUpload" class="mt-5" onclick="$('#pdf_file').click();" title="UPLOAD FILE">
            <i class="fa-solid fa-file-arrow-up" style="zoom: 1500%;"></i><br><br>
            <h3>CLICK HERE TO UPLOAD FILE</h3>
        </center>
    `);
}

setInterval(() => {
    if($('.imgPreview').length){
        $('#pagi').show();
    }
    else{
        $('#pagi').hide();

    }
    if($('#pagi').is(':visible')){
        $('.imgPreview:not([id])').each(function(index){
            var newId = 'imgPreview' + (index + 1);
            $(this).attr('id', newId);
        });
    }
}, 0);

function formRestrictions(data){
    if(current_role == 'ENCODER'){
        if(data.remarks){
            $('#remarks_text').val(data.remarks);
            $('#remarks_div').show();
        }
        else{
            $('#remarks_text').val('');
            $('#remarks_div').hide();
        }
        if(data.status == 'VALID'){
            if(($('#current_user_name').val() != $('#uploaded_by').val())){
                $('.form_disable').prop('disabled', true);
                $('#file_div').empty().append(`
                    <div class="col mt-2">
                        <span class="pdf_file"></span>
                    </div>`
                );
                $('.divReplaceFile').hide();
                $('#btnEdit').hide();
            }
            else{
                $('.form_disable').prop('disabled', true);
                $('#file_div').empty().append(`
                    <div class="col mt-2">
                        <span class="pdf_file"></span>
                    </div>`
                );
                $('.divReplaceFile').hide();
                $('#btnEdit').hide();
            }
        }
        else{
            if(($('#current_user_name').val() != $('#uploaded_by').val())){
                $('.form_disable').prop('disabled', true);
                $('#file_div').empty().append(`
                    <div class="col mt-2">
                        <span class="pdf_file"></span>
                    </div>`
                );
                setTimeout(() => {
                    $('.divReplaceFile').hide();
                }, 200);
                $('#btnEdit').hide();
            }
            else{
                $('.form_disable').prop('disabled', false);
                $('#file_div').empty().append(`
                    <div class="col mt-2">
                        <span class="pdf_file"></span>
                    </div>`
                );
                setTimeout(() => {
                    $('.divReplaceFile').show();
                }, 200);
                $('#btnEdit').show();
            }
        }
    }

    if(current_role == 'ADMIN'){
        $('#remarks_div').show();
        $('#remarks_text').val(data.remarks);
        if(data.status == 'VALID'){
            $('#btnApprove').hide();
            $('#btnDisapprove').hide();
            $('#btnReturn').show();
            setTimeout(() => {
                $('.divReplaceFile').hide();
            }, 100);
            $('#file_div').empty().append(`
                <div class="col mt-2">
                    <span class="pdf_file"></span>
                </div>`
            );
        }
        else{
            $('#btnApprove').show();
            $('#btnDisapprove').show();
            $('#btnReturn').hide();
            setTimeout(() => {
                $('.divReplaceFile').hide();
            }, 100);
            $('#file_div').empty().append(`
                <div class="col mt-2">
                    <span class="pdf_file"></span>
                </div>`
            );
        }
    }

    if(current_role == 'BOSS' || current_role == 'VIEWER'){
        $('.form_disable').prop('disabled', true);
        setTimeout(() => {
            $('.divReplaceFile').hide();
            $('#btnEdit').hide();
        }, 100);
        $('#file_div').empty().append(`
            <div class="col mt-2">
                <span class="pdf_file"></span>
            </div>`
        );
    }
}

setInterval(() => {
    $('body').css('padding-right','0px');
}, 0);