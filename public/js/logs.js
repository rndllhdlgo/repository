var table;
$(document).ready(function(){
    table = $('table.userlogsTable').DataTable({
        language: {
            info: "Showing _START_ to _END_ of _TOTAL_ Activities",
            lengthMenu: "Show _MENU_ Activities",
            emptyTable: "NO DATA AVAILABLE",
        },
        processing: true,
        serverSide: false,
        ajax:{
            url: '/index/data',
            "dataType": "json",
            "error": function(xhr, error, thrown){
                if(xhr.status == 500){
                    $('#loading').hide();
                    Swal.fire({
                        title: 'DATA PROBLEM!',
                        html: '<h3>Data does not load properly.<br>Please refresh the page, or if it keeps happening, contact the <b>ADMINISTRATOR</b>.</h3>',
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
            }
        },
        columns: [
            {
                data: 'created_at',
                "render": function(data, type, row){
                    if(type === "sort" || type === 'type'){
                        return data;
                    }
                    return moment(data).format('MMMM DD, YYYY, h:mm A');
                }, width: '16vh'
            },
            { data: 'username', width: '22vh' },
            { data: 'role', width: '10vh' },
            {
                data: 'activity',
                "render":function(data,type,row){
                    activity = row.activity.replaceAll(" 【", "<br>【");
                    return(`<div style="white-space: normal; width: 52vw;">${activity}</div>`);;
                }
            },
        ],
        order: [],
        initComplete: function(){
            $(document).prop('title', $('#page-name').text());
            $('#loading').hide();
        }
    });

    $('.filter-select').on('change', function(){
        table.column($(this).data('column')).search(!$(this).val()?'':$(this).val(),true,false,true).draw();
    });

    $('.filter-input').on('keyup search', function(){
        table.column($(this).data('column')).search($(this).val()).draw();
    });

    $('#userlogsTable tbody').on('click', 'tr', function(){
        if(!table.data().any()){ return false; }
        var value = table.row(this).data();
        Swal.fire({
            title: moment(value.created_at).format('dddd, MMMM DD, YYYY, h:mm:ss A'),
            html: `<h4>${value.username} [${value.role}]</h4><br><ol style="text-align: left !important;font-weight:600 !important;">${decodeHtml(value.activity).replaceAll(" 【", "<li>【")}</li></ol>`,
            icon: 'info',
            width: 900
        });
    });

});