var table;
$(document).ready(function(){
    table = $('table.drTable').DataTable({
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:{
            left: 3,
        },
        dom: 'ltrip',
        aLengthMenu:[[10,25,50,100,500,1000,-1], [10,25,50,100,500,1000,"All"]],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ DELIVERY RECEIPT",
            lengthMenu: "Show _MENU_ DELIVERY RECEIPT",
            emptyTable: "NO DATA AVAILABLE",
        },
        processing: true,
        serverSide: false,
        order: [],
        columnDefs: [
            {
                "targets": [6,7],
                "visible": false,
                "searchable": true
            },
        ],
        ajax: {
            url: 'dr_data'
        },
        columns: [
            { data: 'delivery_receipt', name:'delivery_receipt'},
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
                data: 'date_created',
                name: 'date_created',
                "render":function(data,type,row){
                    return formatDate(data);
                }
            },
            {
                data: 'date_received',
                name: 'date_received',
                "render":function(data,type,row){
                    return formatDate(data);
                }
            },
            { data: 'purchase_order', name:'purchase_order'},
            { data: 'sales_order', name:'sales_order'},
            {
                data: 'status',
                name: 'status',
                "render":function(data,type,row){
                    return `<span class="${data == 'valid' ? 'text-success' : 'text-danger'}"><b>${data.toUpperCase()}</b></span>`;
                },
            },
        ],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
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
            for(var i=0; i<=8; i++){
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

$('#drAdd').on('click',function(){
    $('#drTitle').html('ADD DELIVERY RECEIPT');
    $('#delivery_receipt').prop('disabled',false);
    $('#form_reset').trigger('reset');
    $('.pdf_file').empty();
    $('#btnUploadPdf').show();
    $('#btnApprove').hide();
    $('#btnSave').show();
    $('#btnEdit').hide();
    $('#btnClear').show();
    $('.req').hide();

    $('#file_div').empty().append(`
            <div class="col-7">
                <button type="button" id="txtUploadPdf" class="btn btn-primary bp" onclick="$('#pdf_file').click();">
                    <i class="fa-solid fa-file-arrow-up mr-1"></i>
                    <span id="txtUploadPdf">UPLOAD FILE</span>
                </button>
                <span style="visibility:hidden;">
                    <input type="file" id="pdf_file" name="pdf_file" class="form-control requiredField" accept=".pdf"/>
                </span>
            </div>`
    );

    $('#drModal').modal('show');
});

function save_pdf(){
    var formData = new FormData();

    var delivery_receipt = $('#delivery_receipt').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var date_created = $('#date_created').val();
    var date_received = $('#date_received').val();
    var purchase_order = $('#purchase_order').val();
    var sales_order = $('#sales_order').val();
    var pdf_file = $('#pdf_file').prop('files')[0];

    formData.append('delivery_receipt', delivery_receipt);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('date_created', date_created);
    formData.append('date_received', date_received);
    formData.append('purchase_order', purchase_order);
    formData.append('sales_order', sales_order);
    formData.append('pdf_file', pdf_file);

    $.ajax({
        url: '/save_dr',
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
            if(response == 'invalid'){
                Swal.fire({
                    title: 'SAVE SUCCESS',
                    html: "FILE UPLOADED SUCCESSFULLY BUT NOT VALIDATED",
                    icon: 'warning'
                });
                $('#drModal').modal('hide');
            }
            else if(response == 'Invalid file format'){
                Swal.fire({
                    title: 'SAVE FAILED',
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
                $('#drModal').modal('hide');
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

$(document).on('click','table.drTable tbody tr',function(){
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();

    $('#drTitle').html('DELIVERY RECEIPT DETAILS');
    $('.disabled').prop('disabled',true);

    if(current_role == 'ADMIN' || current_role == 'ENCODER'){
        $('.enabled').prop('disabled',false);
        $('#btnEdit').show();
    }
    else{
        $('.enabled').prop('disabled',true);
        $('.footer_hide').hide();
    }

    $('#entry_id').val(data.id);
    $('#delivery_receipt').val(data.delivery_receipt);
    $('#company').val(data.company);
    $('#client_name').val(data.client_name);
    $('#branch_name').val(data.branch_name);
    $('#date_created').val(data.date_created);
    $('#date_received').val(data.date_received);
    $('#purchase_order').val(data.purchase_order);
    $('#sales_order').val(data.sales_order);
    $('#btnUploadPdf').show();
    if(data.status == 'valid'){
        $('#file_div').empty().append(`
            <div class="col mt-2">
                <span class="pdf_file"></span>
            </div>`
        );
        $('#btnApprove').hide();
        $('#delivery_receipt').prop('disabled',true);
    }
    else{
        $('#file_div').empty().append(`
            <div class="col-4">
                <button type="button" id="txtUploadPdf" class="btn btn-primary bp" onclick="$('#pdf_file').click();">
                    <i class="fa-solid fa-file-arrow-up mr-1"></i>
                    <span id="txtUploadPdf">REPLACE FILE</span>
                </button>
                <span class="d-none">
                    <input type="file" id="pdf_file" name="pdf_file" class="form-control " accept=".pdf"/>
                </span>
            </div>
            <div class="col mt-2">
                <span class="pdf_file"></span>
            </div>`
        );
        $('#btnApprove').show();
        $('#delivery_receipt').prop('disabled',false);
    }
    $('.pdf_file').html(`<b>CURRENT PDF FILE:</b> <a href="/storage/delivery_receipt/${data.created_at.substr(0, 10)}/${data.pdf_file}" target="_blank" title="OPEN FILE">${data.pdf_file}</a>`);

    $('#btnSave').hide();
    $('#btnClear').hide();
    $('#drModal').modal('show');
});