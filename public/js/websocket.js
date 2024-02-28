$(document).ready(function(){
    if(current_role == 'ADMIN'){
        try{
            setTimeout(() => {
                window.Echo.channel('NewLogs')
                    .listen('.App\\Events\\NewLogs', (e) => {
                        console.log(e.data);
                        table.ajax.reload();
                    });

                window.Echo.channel('NewSi')
                    .listen('.App\\Events\\NewSi', (e) => {
                        console.log(e.data+" SI");
                        $('#si_notif').html(e.data);
                        if($('#current_page').val() == 'si'){
                            table.ajax.reload();
                        }
                    });

                window.Echo.channel('NewCr')
                    .listen('.App\\Events\\NewCr', (e) => {
                        console.log(e.data+" CR");
                        $('#cr_notif').html(e.data);
                        if($('#current_page').val() == 'cr'){
                            table.ajax.reload();
                        }
                    });

                window.Echo.channel('NewBs')
                    .listen('.App\\Events\\NewBs', (e) => {
                        console.log(e.data+" BS");
                        $('#bs_notif').html(e.data);
                        if($('#current_page').val() == 'bs'){
                            table.ajax.reload();
                        }
                    });

                window.Echo.channel('NewOr')
                    .listen('.App\\Events\\NewOr', (e) => {
                        console.log(e.data+" OR");
                        $('#or_notif').html(e.data);
                        if($('#current_page').val() == 'or'){
                            table.ajax.reload();
                        }
                    });

                window.Echo.channel('NewDr')
                    .listen('.App\\Events\\NewDr', (e) => {
                        console.log(e.data+" DR");
                        $('#dr_notif').html(e.data);
                        if($('#current_page').val() == 'dr'){
                            table.ajax.reload();
                        }
                    });
            }, 200);
        }
        catch(error){
            console.error('Error initializing Echo:', error);
        }
    }
});