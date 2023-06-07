var table;
$(document).ready(function(){
    table = $('table.drTable').DataTable({
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
        ajax: {
            url: 'delivery_receipt_data'
        },
        columns: [
            { data: 'delivery_receipt', name:'delivery_receipt'},
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
            { data: 'pdf_file', name:'pdf_file'}
        ],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            $('#loading').hide();
        }
    });

    setInterval(() => {
        $('th input').on('click', function(e){
            e.stopPropagation();
        });
    }, 0);

    $('.filter-input').on('keyup search', function(){
        table.column($(this).data('column')).search($(this).val()).draw();
    });
});

$('#drAdd').on('click',function(){
    $('#drTitle').html('ADD DELIVERY RECEIPT');
    $('#form_reset').trigger('reset');

    $('.req').hide();
    $('#drModal').modal('show');
});

function save_pdf(){
    var delivery_receipt = $('#delivery_receipt').val();
    var client_name = $('#client_name').val();
    var branch_name = $('#branch_name').val();
    var date_created = $('#date_created').val();
    var date_received = $('#date_received').val();
    var purchase_order = $('#purchase_order').val();
    var sales_order = $('#sales_order').val();
    var pdf_file = $('#pdf_file').prop('files')[0];

    var formData = new FormData();

    formData.append('delivery_receipt', delivery_receipt);
    formData.append('client_name', client_name);
    formData.append('branch_name', branch_name);
    formData.append('date_created', date_created);
    formData.append('date_received', date_received);
    formData.append('purchase_order', purchase_order);
    formData.append('sales_order', sales_order);
    formData.append('pdf_file', pdf_file);

    $.ajax({
        url: '/save_delivery_receipt',
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
                $('#siModal').modal('hide');
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
                save_pdf();
            }
        }
    });
});

$(document).on('click','table.drTable tbody tr',function(){
    var data = table.row(this).data();

    $('#drTitle').html('DELIVERY RECEIPT DETAILS');
    $('.disabled').prop('disabled',true);

    $('#delivery_receipt').val(data.delivery_receipt);
    $('#client_name').val(data.client_name);
    $('#branch_name').val(data.branch_name);
    $('#date_created').val(data.date_created);
    $('#date_received').val(data.date_received);
    $('#purchase_order').val(data.purchase_order);
    $('#sales_order').val(data.sales_order);
    $('#pdf_file').hide();
    $('.pdf_file').html(`<b>PDF FILE:</b> <a href="/storage/delivery_receipt/${data.pdf_file}" target="_blank" title="OPEN FILE">${data.pdf_file}</a>`);

    $('#btnSave').hide();
    $('#btnClear').hide();
    $('#drModal').modal('show');
});