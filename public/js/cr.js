var table;
$(document).ready(function(){
    table = $('table.crTable').DataTable({
        scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        fixedColumns:{
            left: 3,
        },
        dom: 'ltrip',
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
                "targets": [5,6],
                "visible": false,
                "searchable": true
            },
        ],
        ajax: {
            url: 'cr_data'
        },
        columns: [
            { data: 'collection_receipt', name:'collection_receipt'},
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
            { data: 'sales_order', name:'sales_order'},
            { data: 'sales_invoice', name:'sales_invoice'},
            { data: 'pdf_file', name:'pdf_file'}
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
    $('.disabled').prop('disabled',false);
    $('.enabled').prop('disabled',false);
    $('#form_reset').trigger('reset');
    $('.pdf_file').empty();
    $('#pdf_file').show();
    $('#btnSave').show();
    $('#btnUpdate').hide();
    $('#btnClear').show();
    $('.req').hide();

    $('#crModal').modal('show');
});

function save_pdf(){
    var collection_receipt = $('#collection_receipt').val();
    var company = $('#company').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var date_created = $('#date_created').val();
    var sales_order = $('#sales_order').val();
    var sales_invoice = $('#sales_invoice').val();
    var pdf_file = $('#pdf_file').prop('files')[0];

    var formData = new FormData();

    formData.append('collection_receipt', collection_receipt);
    formData.append('company', company);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('date_created', date_created);
    formData.append('sales_order', sales_order);
    formData.append('sales_invoice', sales_invoice);
    formData.append('pdf_file', pdf_file);

    $.ajax({
        url: '/save_collection_receipt',
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
            if(response != 'success'){
                Swal.fire({
                    title: 'SAVE FAILED',
                    html: "<b>"+response+"</b>",
                    icon: 'error',
                });
                return false;
            }
            else{
                Swal.fire({
                    title: 'SAVE SUCCESS',
                    icon: 'success',
                    timer: 2000
                });
                $('#crModal').modal('hide');
                setTimeout(function(){location.reload();}, 2000);
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
    if(!table.data().any()){ return false; }
    var data = table.row(this).data();

    $('#crTitle').html('COLLECTION RECEIPT DETAILS');
    $('.disabled').prop('disabled',true);

    if(current_role == 'ADMIN' || current_role == 'ENCODER'){
        $('.enabled').prop('disabled',false);
        $('#btnUpdate').show();
    }
    else{
        $('.enabled').prop('disabled',true);
        $('.footer_hide').hide();
    }

    $('#collection_receipt').val(data.collection_receipt);
    $('#company').val(data.company);
    $('#client_name').val(data.client_name);
    $('#branch_name').val(data.branch_name);
    $('#date_created').val(data.date_created);
    $('#sales_order').val(data.sales_order);
    $('#sales_invoice').val(data.sales_invoice);
    $('#pdf_file').hide();
    $('.pdf_file').html(`<b>PDF FILE:</b> <a href="/storage/collection_receipt/${data.pdf_file}" target="_blank" title="OPEN FILE">${data.pdf_file}</a>`);

    $('#btnSave').hide();
    $('#btnClear').hide();
    $('#crModal').modal('show');
});