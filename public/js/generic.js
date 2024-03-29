$(document).on('contextmenu', '.preventRightClick', function(e){
    e.preventDefault();
});

function addModal(modal_title, modal_heading, modal_id){
    $('#entry_id').val('');
    $('#entry_id').attr('updated_at', '');
    $('#entry_id').attr('check_table', '');
    $(`#${modal_title}`).html(`${modal_heading}`);
    $('#form_reset').trigger('reset');
    $('.pdf_file').empty();
    $('#btnApprove').hide();
    $('#btnDisapprove').hide();
    $('#btnSave').show();
    $('#btnEdit').hide();
    $('#btnClear').show();
    $('#uploaded_by_div').hide();
    $('#status_div').hide();
    $('.form_disable').prop('disabled', false);
    $('.divReplaceFile').hide();
    $('.req').remove();
    $('#btnRequestEdit').hide();

    if($('#btnTogglePreview').text() == 'Minimize'){
        $('#btnTogglePreview').click();
    }
    $('#file_div').empty().append(`
        <div class="col-7 d-none">
            <button type="button" class="btn btn-primary bp" onclick="$('#pdf_file').click();">
                <i class="fa-solid fa-file-arrow-up mr-1"></i>
                <span>UPLOAD FILE</span>
            </button>
            <span style="visibility:hidden;">
                <input type="file" id="pdf_file" name="pdf_file[]" class="form-control requiredField" accept=".jpeg,.jpg,.png,.pdf" multiple/>
            </span>
        </div>`
    );
    resetUpload();
    $(`#${modal_id}`).modal('show');
}

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
                            $('.modal').modal('hide');
                            table.ajax.reload();
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
            resetUpload();
        }
    });
}

function save_upload(response){
    $('#loading').hide();
    if(response == 'NO CHANGES'){
        Swal.fire("NO CHANGES FOUND", "", "error");
    }
    else if(response == 'MAX SIZE'){
        Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
    }
    else if(response == 'FILE EXTENSION'){
        Swal.fire('INVALID file type!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg.', 'error');
    }
    else if(response == 'FOR VALIDATION'){
        Swal.fire({
            title: 'SUBMIT SUCCESS',
            html: 'SUBMITTED FOR VALIDATION',
            icon: 'success'
        });
        $('.modal').modal('hide');
        table.ajax.reload();
        return false;
    }
    else if(response == 'INVALID'){
        Swal.fire({
            title: 'SUBMIT FAILED',
            html: "INVALID FILE FORMAT",
            icon: 'warning',
        });
    }
    else if(response == 'DUPLICATE'){
        Swal.fire({
            title: 'ALREADY EXISTS',
            icon: 'error'
        });
    }
    else if(response == 'false'){
        Swal.fire({
            title: 'SUBMIT ERROR',
            html: 'FILE SUBMIT ERROR',
            icon: 'error'
        });
    }
    else{
        Swal.fire({
            title: 'SUBMIT SUCCESS',
            html: 'SUBMITTED FOR VALIDATION',
            icon: 'success'
        });
        $('.modal').modal('hide');
        return false;
    }
    resetUpload();
}

var pdf_embed;
$(document).on('change', '#pdf_file', function(e){
    if(!e.target.files.length){
        $('#btnViewFile').click();
        return false;
    }
    var files_length = $("#pdf_file").get(0).files.length;
    var error_ext = 0;
    var error_mb = 0;
    var total_file_size = 0;
    pdf_embed = '';
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
        resetUpload();
        return false;
    }
    else if(error_ext > 0){
        Swal.fire('INVALID file type!', 'Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        resetUpload();
        return false;
    }
    else if(error_mb > 0){
        Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
        resetUpload();
        return false;
    }
    else if(total_file_size > (5242880*4)){
        Swal.fire('EXCEEDED maximum total file size (20 MB)!', 'Please upload valid file/s with total file size not greater than 20 MB.', 'error');
        $('#pdf_file').val('');
        $('#pdf_file').focus();
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

        if(['pdf','png','jpg','jpeg'].includes(fileType)){
            if(fileType === 'pdf'){
                pdf_embed = $(`<embed width="100%" height="700px">`).attr("src", e.target.result).addClass("pdf-embed");
                $("#displayFile").append(pdf_embed);
            }
            else{
                var img_embed = $(`<img class="imgPreview" style='width:100%; display: none;'>`).attr("src", e.target.result);
                $("#displayFile").append(img_embed);
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
    $('.divReplaceFile').hide();
    if($('#entry_id').val()){
        $('.divReplaceFile').show();
        $('#btnViewFile').click();
    }
    else{
        $('#pdf_file').val('');
        $('.pItem').remove();

        $('#displayFile').empty().append(`
            <center id="logoUpload" class="mt-5" onclick="$('#pdf_file').click();" title="UPLOAD FILE" style="cursor: pointer;">
                <i class="fa-solid fa-file-arrow-up" style="zoom: 1500%;"></i><br><br>
                <h3>CLICK HERE TO UPLOAD FILE</h3>
                <span style="white-space: normal; width: 45vw;">Please upload file/s with valid file type like the following: pdf, png, jpg or jpeg; AND with file size not greater than 2.5 MB each.</span><br>
            </center>
        `);
    }
}

function formRestrictions(data){
    $('#btnRequestEdit').hide();
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
                $('#btnRequestEdit').show();
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
        $('.form_disable').prop('disabled', false);
        $('#btnEdit').show();
        $('#remarks_div').show();
        $('#remarks_text').val(data.remarks);
        if(data.status == 'FOR CORRECTION'){
            $('.form_disable').prop('disabled', true);
            Swal.fire({
                title: "FOR CORRECTION",
                html: "Please click <b class='text-danger'>RETURN TO ENCODER</b> button for correction.",
                icon: "info"
            });
            $('#btnApprove').hide();
            $('#btnDisapprove').hide();
            $('#btnEdit').hide();
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
        else if(data.status == 'VALID'){
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
    resetUpload();
});

$(document).on('click', '#btnTogglePreview', function(){
    if($(this).text() == 'Maximize'){
        $(this).html(`Minimize<i class="fa-solid fa-magnifying-glass-minus fa-lg ml-2"></i>`);
        $('.left-side').hide();
        if(!$('#pdf_file').val()){
            $('#btnViewFile').click();
        }
        else{
            if(pdf_embed){
                $('#displayFile').empty().append(pdf_embed);
            }
        }
    }
    else{
        $(this).html(`Maximize<i class="fa-solid fa-magnifying-glass-plus fa-lg ml-2"></i>`);
        $('.left-side').show();
        if(!$('#pdf_file').val()){
            $('#btnViewFile').click();
        }
        else{
            if(pdf_embed){
                $('#displayFile').empty().append(pdf_embed);
            }
        }
    }
});

$(document).on('click', '#btnViewFile', function(){
    $('#loading').show();
    $('#pdf_file').val('');
    pdf_embed = '';
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
                <embed src="${file_url}?${rand_str}" width="100%" height="700px"/>
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
                        Swal.fire({
                            title: 'INVALIDATE SUCCESS',
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

$(document).ready(function(){
    if(current_role == 'ADMIN'){
        try {
            setTimeout(() => {
                // window.Echo.channel('NewSi')
                //     .listen('.App\\Events\\NewSi', (e) => {
                //         $('#si_notif').html(e.data);
                //         if($('#current_page').val() == 'si'){
                //             table.ajax.reload();
                //         }
                //     });

                // window.Echo.channel('NewCr')
                //     .listen('.App\\Events\\NewCr', (e) => {
                //         $('#cr_notif').html(e.data);
                //         if($('#current_page').val() == 'cr'){
                //             table.ajax.reload();
                //         }
                //     });

                // window.Echo.channel('NewBs')
                //     .listen('.App\\Events\\NewBs', (e) => {
                //         $('#bs_notif').html(e.data);
                //         if($('#current_page').val() == 'bs'){
                //             table.ajax.reload();
                //         }
                //     });

                // window.Echo.channel('NewOr')
                //     .listen('.App\\Events\\NewOr', (e) => {
                //         $('#or_notif').html(e.data);
                //         if($('#current_page').val() == 'or'){
                //             table.ajax.reload();
                //         }
                //     });

                // window.Echo.channel('NewDr')
                //     .listen('.App\\Events\\NewDr', (e) => {
                //         $('#dr_notif').html(e.data);
                //         if($('#current_page').val() == 'dr'){
                //             table.ajax.reload();
                //         }
                //     });
            }, 200);
        } catch (error) {
            console.error('Error initializing Echo:', error);
        }

        // setInterval(function(){
        //     if($('#loading').is(':hidden') && standby == false){
        //         if($('.modal_repo').is(':visible') && $('#entry_id').attr('updated_at') && $('#entry_id').attr('check_table')){
        //             var checking = 'modal';
        //             var check_table = $('#entry_id').attr('check_table');
        //             var check_current_id = $('#entry_id').val();
        //             var check_updated_at = $('#entry_id').attr('updated_at');
        //             changed_id = '';
        //         }
        //         else{
        //             var checking = 'default';
        //             changed_id = '';
        //         }

        //         var ajaxData = { checking: checking };
        //         if(checking === 'modal'){
        //             ajaxData.check_table = check_table;
        //             ajaxData.check_current_id = check_current_id;
        //             ajaxData.check_updated_at = check_updated_at;
        //         }

        //         $.ajax({
        //             async: false,
        //             url: "/notif_update",
        //             data: ajaxData,
        //             success: function(data){
        //                 if(data.result == 'true'){
        //                     changed_id = data.changed_id;
        //                 }
        //                 if(parseInt(changed_id) == parseInt($('#entry_id').val())){
        //                     changed_id = '';
        //                     if($(".swal2-container:visible").length == 0){
        //                         Swal.fire({
        //                             title: 'ENTRY UPDATED',
        //                             html: 'Another user updated this entry.',
        //                             icon: 'warning',
        //                             allowOutsideClick: false,
        //                             allowEscapeKey: false,
        //                             confirmButtonText: 'CLOSE'
        //                         }).then((save) => {
        //                             if(save.isConfirmed){
        //                                 $('.btnClose').click();
        //                             }
        //                         });
        //                     }
        //                 }
        //                 if(data.user_update != $('#current_updated_at').val()){
        //                     $('#current_updated_at').val(data.user_update);
        //                     window.location.reload();
        //                 }
        //             }
        //         });
        //     }
        // }, 1100);
    }
});


setInterval(() => {
    $('body').css('padding-right','0px');
    $('#uploaded_by').prop('disabled', true);

    if($('#entry_id').val() && $('#pdf_file').val() && $('#btnReplaceFile').is(':visible')){
        $('#btnResetFile').show();
    }
    else{
        $('#btnResetFile').hide();
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

var pane_index = [];
$(document).on('click', '.buttons-columnVisibility', function(){
    var idxToAdd = parseInt($(this).attr('data-cv-idx'));
    if(idxToAdd > 4){
        return false;
    }
    if(!$(this).hasClass('dt-button-active') && pane_index.indexOf(idxToAdd) === -1){
        pane_index.push(idxToAdd);
    }
    else{
        pane_index.splice(pane_index.indexOf(idxToAdd), 1);
    }
    $('.left-pane').each(function(){
        if(pane_index.indexOf(parseInt($(this).attr('pane-index'))) === -1){
            $(this).removeClass('always-default');
        }
        else{
            if(!$(this).hasClass('always-default'))
            $(this).addClass('always-default');
        }
    });
});

$(document).on('click','#btnRequestEdit', function(){
    Swal.fire({
        title: 'Request ADMIN to Return to ENCODER for correction?',
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
                url: "/requestEdit",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    entry_id: $('#entry_id').val(),
                    current_page: $('#current_page').val(),
                },
                success: function(data){
                    if(data == 'true'){
                        $('#loading').hide();
                        Swal.fire("REQUEST SUCCESS", "", "success");
                        $('.modal').modal('hide');
                    }
                    else{
                        $('#loading').hide();
                        Swal.fire("REQUEST FAILED", "", "error");
                    }
                }
            });
        }
    });
});

function tableError(){
    Swal.fire({
        title: 'DATA PROBLEM!',
        html: '<h4>Data does not load properly.<br>Please refresh the page, or if it keeps happening, contact the <b>ADMINISTRATOR</b>.</h4>',
        confirmButtonText: "REFRESH",
        icon: 'error',
        allowEscapeKey: false,
        allowOutsideClick: false,
        width: 700
    }).then((result) => {
        if(result.isConfirmed){
            window.location.reload();
        }
    });
}