@extends('layouts.app')
@section('content')
<br>

<input type="date" class="form-control" id="start_date"><br>
<input type="date" class="form-control" id="end_date"><br>
<button type="button" class="btn btn-primary" id="submit">SUBMIT</button>
<script>
    $('#loading').hide();
    $('#submit').on('click', function(){
        $.ajax({
            url: '/export_action',
            data: {
                start_date : $('#start_date').val(),
                end_date   : $('#end_date').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response){
            },
        });
    });
</script>
@endsection
