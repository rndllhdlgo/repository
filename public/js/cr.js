var table;
$(document).ready(function(){
    table = $('table.crTable').DataTable({
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:{
            left: 3,
        },
        dom: 'lBftrip',
        buttons: [
            {
                extend: 'colvis',
                text: 'TOGGLE COLUMNS',
                className: 'font-weight-bold'
            }
        ],
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ COLLECTION RECEIPT",
            lengthMenu: "Show _MENU_ COLLECTION RECEIPT",
            emptyTable: "NO DATA AVAILABLE",
        },
        processing: true,
        serverSide: false,
        order: [],
        columnDefs: [
            {
                "targets": [4,5,6],
                "visible": false,
                "searchable": true
            },
        ],
        ajax: {
            url: 'cr_data'
        },
        columns: [
            {
                data: 'collection_receipt',
                name: 'collection_receipt',
                "render":function(data,type,row){
                    if(row.status == 'FOR VALIDATION'){
                        if(current_role == 'BOSS' || current_role == 'VIEWER'){
                            return `<span class="text-success" title="FOR VALIDATION"><b>${data.toUpperCase()}</b></span>`;
                        }
                        else if(row.stage == '1' && current_role == 'ENCODER'){
                            return `<span class="text-success" title="FOR VALIDATION"><i class="fa-solid fa-triangle-exclamation"></i> <b>${data.toUpperCase()}</b></span>`;
                        }
                        else if((row.stage == '0' && current_role == 'ENCODER') || (row.stage == '1' && current_role == 'ADMIN')){
                            return `<span class="text-success" title="FOR VALIDATION"><b>${data.toUpperCase()}</b></span>`;
                        }
                        else if(row.stage == '0' && current_role == 'ADMIN'){
                            return `<span class="text-success" title="FOR VALIDATION"><i class="fa-solid fa-triangle-exclamation"></i> <b>${data.toUpperCase()}</b></span>`;
                        }
                        else{
                            return `<span class="text-success" title="FOR VALIDATION"><b>${data.toUpperCase()}</b></span>`;
                        }
                    }
                    else if(row.status == 'INVALID'){
                        if(current_role == 'ENCODER'){
                            return `<span class="text-danger" title="INVALID"><i class="fa-solid fa-triangle-exclamation"></i> <b>${data.toUpperCase()}</b></span>`;
                        }
                        return `<span class="text-danger" title="INVALID"><b>${data.toUpperCase()}</b></span>`;
                    }
                    return `<span title="VALID">${data.toUpperCase()}</span>`;
                }
            },
            { data: 'company', name:'company'},
            {
                data: 'client_name',
                name: 'client_name',
                "render":function(data,type,row){
                    return data.toUpperCase();
                },
            },
            {
                data: 'branch_name',
                name: 'branch_name',
                "render":function(data,type,row){
                    return data.toUpperCase();
                },
            },
            {
                data: 'uploaded_by',
                name: 'uploaded_by',
                "render":function(data,type,row){
                    return `<div style="white-space: normal; width: 200px;">${data.toUpperCase()}</div>`;
                },
            },
            { data: 'sales_order', name:'sales_order'},
            { data: 'sales_invoice', name:'sales_invoice'},
            {
                data: 'status',
                name: 'status',
                "render":function(data,type,row){
                    if(data == 'FOR VALIDATION'){
                        return `<span class="text-success"><b>${data.toUpperCase()}</b></span>`;
                    }
                    else if(data == 'INVALID'){
                        return `<span class="text-danger"><b>${data.toUpperCase()}</b></span>`;
                    }
                    return `<span>${data.toUpperCase()}</span>`;
                }
            },
        ],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            setInterval(() => {
                $('button[data-cv-idx="0"]').remove();
                $('button[data-cv-idx="1"]').remove();
                $('button[data-cv-idx="2"]').remove();
                $('button[data-cv-idx="3"]').remove();
                $('button[data-cv-idx="7"]').remove();
            }, 0);
            $('.buttons-colvis').click();
            $('.dt-button-collection').hide();
            setTimeout(() => {
                $('body').click();
            }, 200);
            display_search();
            $('#loading').hide();
        }
    });

    $('body').on('click', '.checkboxFilter', function(){
        var column = table.column($(this).attr('data-column'));
        var colnum = $(this).attr('data-column');
        column.visible(!column.visible());
        $('.fl-'+colnum).val('');
        table.column(colnum).search('').draw();
    });

    setInterval(() => {
        if($('.popover-header').is(':visible')){
            for(var i=0; i<=7; i++){
                if(table.column(i).visible()){
                    $('#filter-'+i).prop('checked', true);
                }
                else{
                    $('#filter-'+i).prop('checked', false);
                }
            }
        }
        $('th input').on('click', function(e){
            e.stopPropagation();
        });
    }, 0);

    $('#filter').popover({
        html: true,
        sanitize: false
    });

    $('html').on('click', function(e){
        $('#filter').each(function(){
            if(!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0){
                $('#filter').popover('hide');
            }
        });
    });

    $('.filter-input').on('keyup search', function(){
        table.column($(this).data('column')).search($(this).val()).draw();
    });
});

$('#crAdd').on('click',function(){
    $('#crTitle').html('ADD COLLECTION RECEIPT');
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
    $('.req').hide();

    $('#file_div').empty().append(`
        <div class="col-7 d-none">
            <button type="button" id="txtUploadPdf" class="btn btn-primary bp" onclick="$('#pdf_file').click();">
                <i class="fa-solid fa-file-arrow-up mr-1"></i>
                <span id="txtUploadPdf">UPLOAD FILE</span>
            </button>
            <span style="visibility:hidden;">
                <input type="file" id="pdf_file" name="pdf_file[]" class="form-control requiredField" accept=".jpg,.pdf" multiple/>
            </span>
        </div>`
    );
    resetUpload();
    $('#crModal').modal('show');
});

function save_pdf(){
    var formData = new FormData();

    var collection_receipt = $('#collection_receipt').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var sales_order = $('#sales_order').val();
    var sales_invoice = $('#sales_invoice').val();
    var pdf_files = $('#pdf_file').prop('files');

    formData.append('collection_receipt', collection_receipt);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('sales_order', sales_order);
    formData.append('sales_invoice', sales_invoice);
    for(let i = 0; i < pdf_files.length; i++){
        formData.append('pdf_file[]', pdf_files[i]);
    }

    $.ajax({
        url: '/save_cr',
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
            if(response == 'FOR VALIDATION'){
                Swal.fire({
                    title: 'SUBMIT SUCCESS',
                    html: 'SUBMITTED FOR VALIDATION',
                    icon: 'success'
                });
                $('#crModal').modal('hide');
            }
            else if(response == 'Invalid file format'){
                Swal.fire({
                    title: 'SUBMIT FAILED',
                    html: "INVALID FILE FORMAT",
                    icon: 'warning',
                });
            }
            else if(response == 'Already exist'){
                Swal.fire({
                    title: 'COLLECTION RECEIPT ALREADY EXISTS',
                    icon: 'error'
                });
            }
            else{
                Swal.fire({
                    title: 'SUBMIT ERROR',
                    html: 'FILE SUBMIT ERROR',
                    icon: 'error'
                });
            }
        }
    });
}

$('#btnSave').on('click', function(){
    Swal.fire({
        title: 'Do you want to save?',
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
            if($('#pdf_file').val()){
                $('#loading').show();
                setTimeout(() => {
                    save_pdf();
                }, 200);
            }
        }
    });
});

$(document).on('click','table.crTable tbody tr',function(){
    $('.req').hide();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();

    $('#crTitle').html('COLLECTION RECEIPT DETAILS');

    $('#entry_id').val(data.id);
    $('#collection_receipt').val(data.collection_receipt);
    $('#company').val(data.company);
    $('#client_name').val(data.client_name);
    $('#branch_name').val(data.branch_name);
    $('#uploaded_by').val(data.uploaded_by);
    $('#uploaded_by_div').show();
    $('#uploaded_by').prop('disabled', true);
    $('#sales_order').val(data.sales_order);
    $('#sales_invoice').val(data.sales_invoice);
    $('#status').val(data.status);
    $('#status_div').show();

    formRestrictions(data);

    $('.pdf_file').html(`
        <b>CURRENT PDF FILE: ${data.pdf_file}</b><br>
        <a id="btnViewFile" class="btn btn-link mr-2 preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="#"><i class="fa-solid fa-eye mr-1" title="VIEW FILE"></i>VIEW</a>
        <a id="fetchFileName" class="btn btn-link preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="/storage/collection_receipt/${data.created_at.substr(0, 10)}/${data.pdf_file}" title="DOWNLOAD FILE" download><i class="fa-solid fa-circle-down mr-1"></i>DOWNLOAD</a>
    `);

    $('#btnViewFile').click();
    $('#btnSave').hide();
    $('#btnClear').hide();
    $('#crModal').modal('show');
});