var table;
$(document).ready(function(){
    table = $('table.orTable').DataTable({
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:{
            left: 5,
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
            info: "Showing _START_ to _END_ of _TOTAL_ OFFICIAL RECEIPT",
            lengthMenu: "Show _MENU_ OFFICIAL RECEIPT",
            emptyTable: "NO DATA AVAILABLE",
        },
        // processing: true,
        serverSide: false,
        order: [],
        columnDefs: [
            {
                "targets": [0,1,6,7],
                "visible": false,
                "searchable": true
            },
        ],
        ajax: {
            url: 'or_data',
            "dataType": "json",
            "error": function(xhr, error, thrown){
                if(xhr.status == 500){
                    $('#loading').hide();
                    tableError();
                }
            }
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
                data: 'official_receipt',
                name: 'official_receipt',
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
            }
        ],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            pane_index.push(0, 1);
            setInterval(() => {
                $('button[data-cv-idx="2"]').remove();
                $('button[data-cv-idx="3"]').remove();
                $('button[data-cv-idx="4"]').remove();
                $('button[data-cv-idx="5"]').remove();
                $('button[data-cv-idx="8"]').remove();
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
            for(var i=0; i<=6; i++){
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

$('#orAdd').on('click',function(){
    addModal('orTitle', 'ADD OFFICIAL RECEIPT', 'orModal');
});

function save_pdf(){
    var formData = new FormData();

    var official_receipt = $('#official_receipt').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var sales_order = $('#sales_order').val();
    var pdf_files = $('#pdf_file').prop('files');

    formData.append('official_receipt', official_receipt);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('sales_order', sales_order);
    for(let i = 0; i < pdf_files.length; i++){
        formData.append('pdf_file[]', pdf_files[i]);
    }

    $.ajax({
        url: '/save_or',
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

$(document).on('click','table.orTable tbody tr',function(){
    $('.req').remove();
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();

    $('#orTitle').html('OFFICIAL RECEIPT DETAILS');

    $('#entry_id').val(data.id);
    $('#entry_id').attr('updated_at', adjust_datetime(data.updated_at));
    $('#entry_id').attr('check_table', 'official_receipts');

    $('#official_receipt').val(data.official_receipt);
    $('#company').val(data.company);
    $('#client_name').val(decodeHtml(data.client_name));
    $('#branch_name').val(decodeHtml(data.branch_name));
    $('#uploaded_by').val(data.uploaded_by);
    $('#uploaded_by_div').show();
    $('#uploaded_by').prop('disabled', true);
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
            <a id="fetchFileName" class="btn btn-link preventRightClick d-none" style="cursor: pointer; text-decoration: none;" href="/storage/official_receipt/${data.created_at.substr(0, 10)}/${data.pdf_file}" title="DOWNLOAD FILE" download><i class="fa-solid fa-circle-down mr-1"></i>DOWNLOAD</a>
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
    $('#orModal').modal('show');
});