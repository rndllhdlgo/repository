@extends('layouts.app')
@section('content')
<br>

<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">SALES INVOICE REPOSITORY</span></h4></div>
    </div>
    <div class="col-md form-group">
        <button class="form-control btn btn-custom float-end" id="siAdd" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
    </div>
</div>

<div class="table-responsive container-fluid pt-2">
    <table id="siTable" class="table siTable table-bordered table-striped table-hover display" style="cursor: pointer; width: 100%;">
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
                <td>
                    <input type="search" class="form-control filter-input" data-column="6" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="7" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="8" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>SALES INVOICE NO.</th>
                <th>CLIENT NAME</th>
                <th>BRANCH NAME</th>
                <th>DATE CREATED</th>
                <th>DATE RECEIVED</th>
                <th>PURCHASE ORDER NO.</th>
                <th>SALES ORDER NO.</th>
                <th>DELIVERY RECEIPT NO.</th>
                <th>PDF FILE</th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>

@include('modals.modalSi')
<script src={{asset('js/si.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection