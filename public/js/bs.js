var table;
$(document).ready(function(){
    table = $('table.bsTable').DataTable({
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
        aLengthMenu:[[10,25,50,100, -1], [10,25,50,100, "All"]],
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ BILLING STATEMENT",
            lengthMenu: "Show _MENU_ BILLING STATEMENT",
            emptyTable: "NO DATA AVAILABLE",
        },
        processing: true,
        serverSide: false,
        order: [],
        columnDefs: [
            {
                "targets": [0,1,7,8,9],
                "visible": false,
                "searchable": true
            },
        ],
        ajax: {
            url: 'bs_data'
        },
        columns: [
            {
                data: 'created_at',
                "render": function(data, type, row){
                    if(type === "sort" || type === 'type'){
                        return data;
                    }
                    return moment(data).format('MMM. DD, YYYY h:mm A');
                }, width: '16vh'
            },
            {
                data: 'updated_at',
                "render": function(data, type, row){
                    if(type === "sort" || type === 'type'){
                        return data;
                    }
                    return moment(data).format('MMM. DD, YYYY h:mm A');
                }, width: '16vh'
            },
            {
                data: 'billing_statement',
                name: 'billing_statement',
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
                    else if(row.status == 'FOR CORRECTION' && current_role == 'ADMIN'){
                        return `<span class="text-danger" title="FOR CORRECTION"><i class="fa-solid fa-triangle-exclamation"></i> <b>${data.toUpperCase()}</b></span>`;
                    }
                    return `<span title="VALID">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: 'company',
                name: 'company',
                "render":function(data,type,row){
                    return `<span class="row_id" row_id="${row.id}">${data}</span>`;
                },
            },
            {
                data: 'client_name',
                name: 'client_name',
                "render":function(data,type,row){
                    return `<div style="white-space: normal; width: 300px;">${data.toUpperCase()}</div>`;
                },
            },
            {
                data: 'business_name',
                name: 'business_name',
                "render":function(data,type,row){
                    return `<div style="white-space: normal; width: 300px;">${data.toUpperCase()}</div>`;
                },
            },
            {
                data: 'branch_name',
                name: 'branch_name',
                "render":function(data,type,row){
                    return `<div style="white-space: normal; width: 300px;">${data.toUpperCase()}</div>`;
                },
            },
            {
                data: 'uploaded_by',
                name: 'uploaded_by',
                "render":function(data,type,row){
                    return `<div style="white-space: normal; width: 300px;">${data}</div>`;
                }
            },
            { data: 'sales_order', name:'sales_order'},
            { data: 'purchase_order', name:'purchase_order'},
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
                    else if(data == 'FOR CORRECTION'){
                        return `<span class="text-danger"><b>${data.toUpperCase()}</b></span>`;
                    }
                    return `<span>${data.toUpperCase()}</span>`;
                }
            },
        ],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            pane_index.push(0, 1);
            setInterval(() => {
                $('button[data-cv-idx="2"]').remove();
                $('button[data-cv-idx="3"]').remove();
                $('button[data-cv-idx="4"]').remove();
                $('button[data-cv-idx="5"]').remove();
                $('button[data-cv-idx="6"]').remove();
                $('button[data-cv-idx="10"]').remove();
            }, 0);
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

$('#bsAdd').on('click',function(){
    addModal('bsTitle', 'ADD BILLING STATEMENT', 'bsModal');
});

function save_pdf(){
    var formData = new FormData();

    var billing_statement = $('#billing_statement').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var business_name = $('#business_name').val();
    var branch_name = $('#branch_name').val();
    var date_created = $('#date_created').val();
    var sales_order = $('#sales_order').val();
    var purchase_order = $('#purchase_order').val();
    var pdf_files = $('#pdf_file').prop('files');

    formData.append('billing_statement', billing_statement);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('business_name', business_name);
    formData.append('branch_name', branch_name);
    formData.append('date_created', date_created);
    formData.append('sales_order', sales_order);
    formData.append('purchase_order', purchase_order);
    for(let i = 0; i < pdf_files.length; i++){
        formData.append('pdf_file[]', pdf_files[i]);
    }

    $.ajax({
        url: '/save_bs',
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
        error: function(response){
            $('#loading').hide();
            Swal.fire('EXCEEDED maximum individual file size (2.5 MB)!', 'Please upload valid file/s with file size not greater than 2.5 MB each.', 'error');
            resetUpload();
        }
    });
}

$('#btnSave').on('click', function(){
    Swal.fire({
        title: 'SUBMIT FOR VALIDATION?',
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

$(document).on('click','table.bsTable tbody tr',function(){
    $('.req').remove();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();

    $('#bsTitle').html('BILLING STATEMENT DETAILS');

    $('#entry_id').val(data.id);
    $('#entry_id').attr('updated_at', adjust_datetime(data.updated_at));
    $('#entry_id').attr('check_table', 'billing_statements');

    $('#billing_statement').val(data.billing_statement);
    $('#company').val(data.company);
    $('#client_name').val(decodeHtml(data.client_name));
    $('#business_name').val(decodeHtml(data.business_name));
    $('#branch_name').val(decodeHtml(data.branch_name));
    $('#uploaded_by').val(data.uploaded_by);
    $('#uploaded_by_div').show();
    $('#uploaded_by').prop('disabled', true);
    $('#purchase_order').val(data.purchase_order);
    $('#sales_order').val(data.sales_order);
    $('#status').val(data.status);
    $('#status_div').show();

    formRestrictions(data);

    if(data.pdf_file.includes('storage/')){
        $('.pdf_file').html(`
            <a id="btnViewFile" class="btn btn-link mr-2 preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="#"><i class="fa-solid fa-eye mr-1" title="VIEW FILE"></i>VIEW</a>
            <a id="fetchFileName" class="btn btn-link preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="/${data.pdf_file}" title="DOWNLOAD FILE" download><i class="fa-solid fa-circle-down mr-1"></i>DOWNLOAD</a>
        `);
    }
    else{
        $('.pdf_file').html(`
            <a id="btnViewFile" class="btn btn-link mr-2 preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="#"><i class="fa-solid fa-eye mr-1" title="VIEW FILE"></i>VIEW</a>
            <a id="fetchFileName" class="btn btn-link preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="/storage/billing_statement/${data.created_at.substr(0, 10)}/${data.pdf_file}" title="DOWNLOAD FILE" download><i class="fa-solid fa-circle-down mr-1"></i>DOWNLOAD</a>
        `);
    }

    if($('#btnTogglePreview').text() == 'Minimize'){
        $('#btnTogglePreview').click();
    }
    else{
        $('#btnViewFile').click();
    }
    $('#btnSave').hide();
    $('#btnClear').hide();
    $('#bsModal').modal('show');
});