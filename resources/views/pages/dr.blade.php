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
                <th>
                    <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                    DELIVERY RECEIPT NO.
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
                    CLIENT NAME
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                    BRANCH NAME
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
                    DATE CREATED
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                    DATE RECEIVED
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
                    PURCHASE ORDER NO.
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
                    SALES ORDER NO.
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
                    PDF FILE
                </th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
@include('modals.modalDr')
<script src={{asset('js/dr.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection