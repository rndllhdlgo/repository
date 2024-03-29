@extends('layouts.app')
@section('content')
<br>
<input type="hidden" id="current_page" value="dr">
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">DELIVERY RECEIPT REPOSITORY</span></h4></div>
    </div>
    @role('ENCODER')
        <div class="col-md form-group">
            <button class="form-control btn btn-custom float-end" id="drAdd" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
        </div>
    @endrole
</div>

<div class="ml-2 d-none">
    <a href="#" id="filter" class="text-default" title="Toggle Visible Columns" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content='@include("inc.columnsDeliveryReceipt")'>
        <b class="mr-1">TOGGLE COLUMNS</b>
        <i class="fas fa-filter fa-lg" aria-hidden="true"></i>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </a>
</div>

<table id="drTable" class="table drTable table-bordered table-striped table-hover display w-100" style="cursor: pointer; width: 100%;">
    <thead style="font-weight:bolder" class="bg-default">
        <tr>
            <th class="left-pane always-default" pane-index="0">
                <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                DATE CREATED
            </th>
            <th class="left-pane always-default" pane-index="1">
                <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
                DATE MODIFIED
            </th>
            <th class="left-pane always-default" pane-index="2">
                <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                DR NUMBER
            </th>
            <th class="left-pane always-default" pane-index="3">
                <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
                COMPANY
            </th>
            <th class="left-pane always-default" pane-index="4">
                <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                DELIVERED TO
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
                BUSINESS NAME
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
                BRANCH NAME
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
                UPLOADED BY
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="8" style="border:1px solid #808080"/>
                PURCHASE ORDER NO.
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="9" style="border:1px solid #808080"/>
                SALES ORDER NO.
            </th>
            <th class="th-default">
                <input type="search" class="form-control filter-input" data-column="10" style="border:1px solid #808080"/>
                STATUS
            </th>
        </tr>
    </thead>
</table>
<br>
<hr>
@include('modals.modalDr')
<script src={{asset('js/dr.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection