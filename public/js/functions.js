var current_location = $(location).attr('pathname')+window.location.search;
var current_user = $('#current_user').val();
var current_department = $('#current_department').val();
var current_role = $('#current_role').val();
var current_date = $('#current_date').val();
var current_session = $('#current_session').val();
var current_token = $('#current_token').val();
var current_timeout = $('#current_timeout').val();
var current_email = $('#current_email').val();
var data_update, standby = true;
var si_update = 'NULL', cr_update = 'NULL', bs_update = 'NULL', or_update = 'NULL', dr_update = 'NULL';

setInterval(() => {
    if($('#loading').is(':visible')){
        $('html, body').css({
            overflow: 'hidden',
            height: '100%'
        });
        $('#current_user').focus();
    }
    else{
        $('html, body').css({
            overflow: 'auto',
            height: 'auto'
        });
    }
}, 0);

function idleLogout(){
    var timer;
    window.onload = resetTimer;
    window.onmousedown = resetTimer;
    window.onmousemove = resetTimer;
    window.onclick = resetTimer;
    window.oncontextmenu = resetTimer;
    window.onwheel = resetTimer;
    window.onkeydown = resetTimer;
    function resetTimer(){
        clearTimeout(timer);
        timer = setTimeout(() => {
            $('#loading').show();
            window.location.href = '/logout';
        }, 3600000);
    }
}
idleLogout();

function idleStandby(){
    var timeout;
    window.onmousemove = resetStandby;
    window.onclick = resetStandby;
    window.oncontextmenu = resetStandby;
    window.onwheel = resetStandby;
    window.onkeydown = resetStandby;
    function resetStandby(){
        standby = false;
        clearTimeout(timeout);
        timeout = setTimeout(function(){
            if($('#loading').is(':hidden')){
                standby = true;
            }
        }, 3000);
    }
}
idleStandby();

$(document).ready(function(){
    setInterval(displayClock, 0);
    function displayClock(){
        var today_Date = new Date();
        var today_Month = today_Date.getMonth() + 1;
        var today_Day = today_Date.getDate();
        var today_Year = today_Date.getFullYear();
        var today_Time = new Date().toLocaleTimeString();

        if(today_Month < 10) today_Month = '0' + today_Month.toString();
        if(today_Day < 10) today_Day = '0' + today_Day.toString();

        var today_DateFormat = today_Year + '-' + today_Month + '-' + today_Day;
        today_DateFormat = moment(today_DateFormat, 'YYYY-MM-DD').format('dddd, MMMM DD, YYYY');
        current_datetime.textContent = today_DateFormat + ', ' + today_Time;
    }
});

$(document).on('click', '.page-reload', function(){
    $('#loading').show();
    window.location.href = window.location.href.split(/[?#]/)[0];
});

$('body').on('cut paste', function(){
    setTimeout(function(){
        $(':focus').keyup();
    }, current_timeout);
});

function chosen_select(id){
    $(id).chosen();
    $(id+'_chosen').css({'width':'100%'});
}

setInterval(() => {
    $('.multiple_field').each(function(){
        if($(this).val().length == 0 && $(this).hasClass('requiredField')){
            $(this).next('.chosen-container').addClass('requiredField requiredInput redBorder');
        }
        else{
            $(this).next('.chosen-container').removeClass('requiredField requiredInput redBorder');
            var spanClass = $(this).attr('id') + '_chosen';
            $('.className' + spanClass).remove();
        }
    });

    $('.single_field').each(function(){
        if(!$(this).val() && $(this).hasClass('requiredField')){
            $(this).next('.chosen-container').addClass('requiredField requiredInput redBorder');
        }
        else{
            $(this).next('.chosen-container').removeClass('requiredField requiredInput redBorder');
            var spanClass = $(this).attr('id') + '_chosen';
            $('.className' + spanClass).remove();
        }
    });
}, 0);

function formatDate(dateString){
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var date = new Date(dateString);
    var month = months[date.getMonth()];
    var day = date.getDate();
    var year = date.getFullYear();

    if(month < 10) month = '0' + month.toString();
    if(day < 10) day = '0' + day.toString();

    var formattedDate = month + " " + day + ", " + year;
    return formattedDate;
}

function adjust_datetime(datetime){
    return moment(moment(datetime, 'YYYY-MM-DD HH:mm:ss').format("YYYY-MM-DD HH:mm:ss")).add(8, 'hours').format("YYYY-MM-DD HH:mm:ss")
}

function decodeHtml(str){
    var map = {'&amp;': '&', '&lt;': '<', '&gt;': '>', '&quot;': '"', '&#039;': "'"};
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m){return map[m];});
}

function alpha_numeric(input){
    var letters_only = /[^- ñ a-z _ (0-9)]/gi;
    input.value = input.value.replace(letters_only,"");
}

function formatNumber(n){
    if(n.includes('.')){
        var decimal = n.substr(-3);
        n = n.replace(decimal,'');
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")+decimal;
    }
    else{
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

function sortAmount(num){
    let numStr = num.toFixed(2).toString();
    let [integerPart, decimalPart] = numStr.split('.');
    while(integerPart.length < 13){
        integerPart = '0' + integerPart;
    }
    return integerPart + '.' + decimalPart;
}

function validateEmail(email){
    var regex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(email);
}

var checkRequired = true, checkChanges = true;
setInterval(checkRequiredFields, 0);
function checkRequiredFields(){
    if($('#loading').is(':hidden')){
        if($(".optionalField:visible").length > 0){
            $('.optionalField').each(function(){
                if(!$.trim($(this).val()) && !$(this).is(':focus')){
                    $(this).val('N/A');
                }
            });
        }
        if($(".requiredField:visible").length > 0){
            $('.requiredField').each(function(){
                if(!$.trim($(this).val())){
                    $(this).addClass('requiredInput');
                }
                else{
                    $(this).removeClass('requiredInput');
                }
            });
        }
        if($(".requiredInput:visible").length > 0 || $(".requiredAlert:visible").length > 0){
            checkRequired = false;
            $('.requiredNote').show();
        }
        else{
            checkRequired = true;
            $('.requiredNote').hide();
        }
        if($(".changesNote:visible").length > 0){
            checkChanges = false;
        }
        else{
            checkChanges = true;
        }
        if(checkRequired == true && checkChanges == true && $('#logoUpload').length == 0){
            $('.btnRequired').prop('disabled', false);
            $('.swal2-confirm').prop('disabled', false);
        }
        else{
            $('.btnRequired').prop('disabled', true);
            if($('#checkUpload').is(':hidden')){
                $('.swal2-confirm').prop('disabled', true);
            }
        }
    }
}

$(document).on('focusout', '.requiredField', function(){
    if(!$(this).is('textarea')){
        if(!$(this).val()){
            var alertName = 'className'+$(this).attr('id');
            var alertClass = $('.'+alertName+':visible');
            if(alertClass.length == 0){
                $(this).after('<span class="'+alertName+' req"><div style="height: 18px !important;">&nbsp;</div><p class="requiredValidation"><i class="fas fa-exclamation-triangle"></i> Required Field</p></span>');
            }
            else if(alertClass.length > 1){
                alertClass.remove();
            }
        }
        else{
            alertName = 'className'+$(this).attr('id');
            $('.'+alertName).remove();
        }
    }
});

$(document).ready(function(){
    $('.filter-input').attr('title', 'SEARCH');
});

$(window).on('beforeunload', function(){
    $('#loading').show();
});

$(document).keydown(function(e){
    if(current_role == 'ADMIN'){
        if(e.ctrlKey && e.shiftKey && e.key === 'Z'){
            if($('#btnApprove').is(':visible')){
                $('#btnApprove').click();
            }
        }
        if(e.ctrlKey && e.shiftKey && e.key === 'X'){
            if($('#btnDisapprove').is(':visible')){
                $('#btnDisapprove').click();
            }
        }
    }
});