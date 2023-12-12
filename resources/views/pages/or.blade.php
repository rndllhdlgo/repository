@extends('layouts.app')
@section('content')
<br>
<input type="hidden" id="current_page" value="or">
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">OFFICIAL RECEIPT REPOSITORY</span></h4></div>
    </div>
    @role('ENCODER')
        <div class="col-md form-group">
            <button class="form-control btn btn-custom float-end" id="orAdd" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
        </div>
    @endrole
</div>

<div class="table-responsive container-fluid pt-2" style="min-height: 600px; zoom: 90%;">
    <table id="orTable" class="table orTable table-bordered table-striped table-hover display w-100" style="cursor: pointer; width: 100%;">
        <thead style="font-weight:bolder" class="bg-default">
            <tr>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                    DATE CREATED
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="1" style="border:1px solid #808080"/>
                    DATE MODIFIED
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                    OR NUMBER
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="3" style="border:1px solid #808080"/>
                    COMPANY
                </th>
                <th class="always-default">
                    <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                    RECEIVED FROM
                </th>
                <th class="th-default">
                    <input type="search" class="form-control filter-input" data-column="5" style="border:1px solid #808080"/>
                    BRANCH NAME
                </th>
                <th class="th-default">
                    <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
                    UPLOADED BY
                </th>
                <th class="th-default">
                    <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
                    SALES ORDER NO.
                </th>
                <th class="th-default">
                    <input type="search" class="form-control filter-input" data-column="8" style="border:1px solid #808080"/>
                    STATUS
                </th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
@include('modals.modalOr')
<script src={{asset('js/or.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection