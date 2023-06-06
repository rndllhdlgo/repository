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
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            $('#loading').hide();
        }
    });

    $('.filter-input').on('keyup search', function(){
        table.column($(this).data('column')).search($(this).val()).draw();
    });
});

$('#drAdd').on('click',function(){
    $('#drModal').modal('show');
});

