@extends('layouts.app')
@section('content')
<br>
<input type="hidden" id="current_page" value="cr">
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">COLLECTION RECEIPT REPOSITORY</span></h4></div>
    </div>
    @role('ADMIN|ENCODER')
        <div class="col-md form-group">
            <button class="form-control btn btn-custom float-end" id="crAdd" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
        </div>
    @endrole
</div>

<div class="ml-2">
    <a href="#" id="filter" class="text-default" title="Toggle Visible Columns" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content='@include("inc.columnsCollectionReceipt")'>
        <b class="mr-1">TOGGLE COLUMNS</b>
        <i class="fas fa-filter fa-lg" aria-hidden="true"></i>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </a>
</div>

<div class="table-responsive container-fluid pt-2">
    <table id="crTable" class="table crTable table-bordered table-striped table-hover display" style="cursor: pointer; width: 100%;">
        <thead style="font-weight:bolder" class="bg-default">
            <tr>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                    COLLECTION RECEIPT NO.
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
                    COMPANY
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                    CLIENT NAME
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
                    BRANCH NAME
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                    DATE CREATED
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
                    SALES ORDER NO.
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
                    SALES INVOICE NO.
                </th>
                <th>
                    <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
@include('modals.modalCr')
<script src={{asset('js/cr.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection