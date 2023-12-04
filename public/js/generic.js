$(document).on('contextmenu', '.preventRightClick', function(e){
    e.preventDefault();
});

$(document).on('click', '#btnEdit', function(){
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
            $('#loading').show();
            if($('#pdf_file').length > 0 && $('#pdf_file').get(0).files.length > 0){
                setTimeout(() => {
                    edit_pdf();
                }, 200);
            }
            else{
                $.ajax({
                    url: "/edit",
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        entry_id: $('#entry_id').val(),
                        current_page: $('#current_page').val(),
                        sales_invoice: $('#sales_invoice').val(),
                        collection_receipt: $('#collection_receipt').val(),
                        billing_statement: $('#billing_statement').val(),
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
                        if(data == 'NO CHANGES'){
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
    var entry_id = $('#entry_id').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var sales_order = $('#sales_order').val();
    var purchase_order = $('#purchase_order').val();
    var uploaded_by = $('#uploaded_by').val();
    var pdf_files = $('#pdf_file').prop('files');
    var current_page = $('#current_page').val();
    var url_name = '/edit_'+current_page;

    if(current_page == 'bs'){
        var billing_statement = $('#billing_statement').val();
        formData.append('billing_statement', billing_statement);
    }
    else if (current_page == 'si'){
        var sales_invoice = $('#sales_invoice').val();
        var delivery_receipt = $('#delivery_receipt').val();
        formData.append('sales_invoice', sales_invoice);
        formData.append('delivery_receipt', delivery_receipt);
    }
    else if (current_page == 'cr'){
        var collection_receipt = $('#collection_receipt').val();
        var sales_invoice = $('#sales_invoice').val();
        formData.append('collection_receipt', collection_receipt);
        formData.append('sales_invoice', sales_invoice);
    }
    else if (current_page == 'or'){
        var official_receipt = $('#official_receipt').val();
        formData.append('official_receipt', official_receipt);
    }
    else if (current_page == 'dr'){
        var delivery_receipt = $('#delivery_receipt').val();
        var business_name = $('#business_name').val();
        formData.append('delivery_receipt', delivery_receipt);
        formData.append('business_name', business_name);
    }

    formData.append('entry_id', entry_id);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('sales_order', sales_order);
    formData.append('purchase_order', purchase_order);
    formData.append('uploaded_by', uploaded_by);

    for(let i = 0; i < pdf_files.length; i++){
        formData.append('pdf_file[]', pdf_files[i]);
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
            save_upload(response);
        },
        error: function(){
            $('#loading').hide();
            Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
            $('.divReplaceFile').hide();
            resetUpload();
        }
    });
}

function save_upload(response){
    $('#loading').hide();
    if(response == 'NO CHANGES'){
        Swal.fire("NO CHANGES FOUND", "", "error");
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else if(response == 'MAX SIZE'){
        Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else if(response == 'FILE EXTENSION'){
        Swal.fire('INVALID file type!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg.', 'error');
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else if(response == 'FOR VALIDATION'){
        Swal.fire({
            title: 'SUBMIT SUCCESS',
            html: 'SUBMITTED FOR VALIDATION',
            icon: 'success'
        });
        $('.modal').modal('hide');
    }
    else if(response == 'INVALID'){
        Swal.fire({
            title: 'SUBMIT FAILED',
            html: "INVALID FILE FORMAT",
            icon: 'warning',
        });
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else if(response == 'DUPLICATE'){
        Swal.fire({
            title: 'ALREADY EXISTS',
            icon: 'error'
        });
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else if(response == 'false'){
        Swal.fire({
            title: 'SUBMIT ERROR',
            html: 'FILE SUBMIT ERROR',
            icon: 'error'
        });
        $('.divReplaceFile').hide();
        resetUpload();
    }
    else{
        Swal.fire({
            title: 'SUBMIT SUCCESS',
            html: 'SUBMITTED FOR VALIDATION',
            icon: 'success'
        });
        $('.modal').modal('hide');
    }
}

$(document).on('change', '#pdf_file', function(e){
    var files_length = $("#pdf_file").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    var total_file_size = 0;
    for(var i = 0; i < files_length; ++i){
        var file1=$("#pdf_file").get(0).files[i].name;
        var file_size = $("#pdf_file").get(0).files[i].size;
        var ext = file1.split('.').pop().toLowerCase();
        if($.inArray(ext,['pdf','png','jpg','jpeg'])===-1){
            error_ext++;
        }
        if(file_size > 5242880 / 2){
            error_mb++;
        }
        total_file_size+=file_size;
    }
    if(error_ext > 0 && error_mb > 0){
        Swal.fire({
            title: 'INVALID file type AND EXCEEDED maximum individual file size (2.5 MB)!',
            html: 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg; AND with file size not greater than 2.5 MB each.',
            icon: 'error',
            width: 700
        });
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        $('.divReplaceFile').hide();
        resetUpload();
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        $('.divReplaceFile').hide();
        resetUpload();
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        $('.divReplaceFile').hide();
        resetUpload();
        return false;
    }
    else if(total_file_size > (5242880*4)){
        Swal.fire('EXCEEDED maximum total file size (20 MB)!', 'Please upload valid file/s with total file size not greater than 20 MB.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        $('.divReplaceFile').hide();
        resetUpload();
        return false;
    }

    var files = e.target.files;
    var pdf_count = 0;
    var img_count = 0;
    if(!files.length){
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
            icon: 'error',
            title: 'MAX LIMIT REACHED',
            html: '<span id="checkUpload">Only 1 PDF file can be uploaded.</span>'
        });
        resetUpload();
        return false;
    }
    else if(pdf_count > 0 && img_count > 0){
        Swal.fire({
            icon: 'error',
            title: 'INVALID COMBINATION',
            html: '<span id="checkUpload">Cannot upload different file type/s combination.</span>'
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
                var img = $(`<img class="imgPreview" style='width:100%; display: none;'>`).attr("src", e.target.result);
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
    $('#pdf_file').val('');
    $('.pItem').remove();

    $('#displayFile').empty().append(`
        <center id="logoUpload" class="mt-5" onclick="$('#pdf_file').click();" title="UPLOAD FILE">
            <i class="fa-solid fa-file-arrow-up" style="zoom: 1500%;"></i><br><br>
            <h3>CLICK HERE TO UPLOAD FILE</h3>
            <span style="white-space: normal; width: 45vw;">Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg; AND with file size not greater than 2.5 MB each.</span><br>
        </center>
    `);
}

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
            if((current_user_name != $('#uploaded_by').val())){
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
            if((current_user_name != $('#uploaded_by').val())){
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

$(document).on('click', '#btnClear', function(){
    $('.req').remove();
    $('.divReplaceFile').hide();
    resetUpload();
});

$(document).on('click', '#btnTogglePreview', function(){
    if($(this).text() == 'Maximize'){
        $(this).html(`Minimize<i class="fa-solid fa-magnifying-glass-minus fa-lg ml-2"></i>`);
        $('.left-side').hide();
        $('#btnViewFile').click();
    }
    else{
        $(this).html(`Maximize<i class="fa-solid fa-magnifying-glass-plus fa-lg ml-2"></i>`);
        $('.left-side').show();
        $('#btnViewFile').click();
    }
});

$(document).on('click', '#btnViewFile', function(){
    $('#loading').show();
    $('#pdf_file').val('');
    var file_url = $('#fetchFileName').attr('href');
    var rand_str = Math.random().toString(36).substring(2,12);
    $.ajax({
        url: '/checkURL',
        async: false,
        data:{
            file_url: file_url
        },
        success: function(data){
            if(data == 'false'){ file_url = '/image/404.jpg'; }
            $('#displayFile').empty().append(`
                <embed src="${file_url}?${rand_str}" width="100%" height="600px"/>
            `);
            $('#loading').hide();
        }
    });
});

$(document).on('click', '#btnApprove', function(){
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
                $('.current_search').val('').keyup();
                $(`.paginate_button[data-dt-idx="0"]`).click();
                setTimeout(() => {
                    $.ajax({
                        url: "/checkNext",
                        async: false,
                        data: {
                            current_location: current_location,
                        },
                        success: function(data){
                            $(`.row_id[row_id="${data}"]`).closest('tr').click();
                        }
                    });
                }, 1100);
            }
            else{
                $('#loading').hide();
                Swal.fire("VALIDATE FAILED", "", "error");
            }
        }
    });
});

$(document).on('click', '#btnDisapprove', function(){
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

$(document).on('click', '#btnReturn', function(){
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
        if($('.modal_repo').is(':visible') && $('#entry_id').attr('updated_at') && $('#entry_id').attr('check_table')){
            var checking = 'modal';
            var check_table = $('#entry_id').attr('check_table');
            var check_current_id = $('#entry_id').val();
            var check_updated_at = $('#entry_id').attr('updated_at');
            changed_id = '';
        }
        else{
            var checking = 'default';
            changed_id = '';
        }

        var ajaxData = { checking: checking };
        if(checking === 'modal'){
            ajaxData.check_table = check_table;
            ajaxData.check_current_id = check_current_id;
            ajaxData.check_updated_at = check_updated_at;
        }

        $.ajax({
            async: false,
            url: "/notif_update",
            data: ajaxData,
            success: function(data){
                if(data.result == 'true'){
                    changed_id = data.changed_id;
                }
                if(parseInt(changed_id) == parseInt($('#entry_id').val())){
                    changed_id = '';
                    if($(".swal2-container:visible").length == 0){
                        Swal.fire({
                            title: 'ENTRY UPDATED',
                            html: 'Another user updated this entry.',
                            icon: 'warning',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'CLOSE'
                        }).then((save) => {
                            if(save.isConfirmed){
                                $('.btnClose').click();
                            }
                        });
                    }
                }
                if(data.user_update != $('#current_updated_at').val()){
                    $('#current_updated_at').val(data.user_update);
                    window.location.reload();
                }
                if(data.si_update != si_update){
                    si_update = data.si_update;
                    $('#si_notif').html(data.si_count);
                    if($('#current_page').val() == 'si'){
                        table.ajax.reload(null, false);
                    }
                }
                if(data.cr_update != cr_update){
                    cr_update = data.cr_update;
                    $('#cr_notif').html(data.cr_count);
                    if($('#current_page').val() == 'cr'){
                        table.ajax.reload(null, false);
                    }
                }
                if(data.bs_update != bs_update){
                    bs_update = data.bs_update;
                    $('#bs_notif').html(data.bs_count);
                    if($('#current_page').val() == 'bs'){
                        table.ajax.reload(null, false);
                    }
                }
                if(data.or_update != or_update){
                    or_update = data.or_update;
                    $('#or_notif').html(data.or_count);
                    if($('#current_page').val() == 'or'){
                        table.ajax.reload(null, false);
                    }
                }
                if(data.dr_update != dr_update){
                    dr_update = data.dr_update;
                    $('#dr_notif').html(data.dr_count);
                    if($('#current_page').val() == 'dr'){
                        table.ajax.reload(null, false);
                    }
                }
            }
        });
    }
}, 1100);

setInterval(() => {
    $('body').css('padding-right','0px');
    $('#uploaded_by').prop('disabled', true);

    if($('#entry_id').val() && $('#pdf_file').val() && $('#btnReplaceFile').is(':visible')){
        $('#btnResetFile').show();
    }
    else{
        $('#btnResetFile').hide();
    }

    if(!$('#entry_id').val()){
        $('#btnTogglePreview').hide();
    }
    else{
        $('#btnTogglePreview').show();
    }

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

$(document).on('keyup search', '.current_search', function(){
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