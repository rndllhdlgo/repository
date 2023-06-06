@extends('layouts.app')
@section('content')
<br>
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">DELIVERY RECEIPT REPOSITORY</span></h4></div>
    </div>
    @role('ADMIN|ENCODER')
        <div class="col-md form-group">
            <button class="form-control btn btn-custom float-end" id="drAdd" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
        </div>
    @endrole
</div>

<div class="table-responsive container-fluid pt-2">
    <table id="drTable" class="table drTable table-bordered table-striped table-hover display" style="cursor: pointer; width: 100%;">
        <thead style="font-weight:bolder" class="bg-default">
            <tr>
                <td>
                    <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>DR NO.</th>
                <th>CLIENT NAME</th>
                <th>DATE CREATED</th>
                <th>DATE RECEIVED</th>
                <th>PO NO.</th>
                <th>FILENAME</th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
@include('modals.modalDr')
<script src={{asset('js/dr.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection