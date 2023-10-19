@extends('layouts.app')
@section('content')
<br>
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">USER ACCOUNTS</span></h4></div>
    </div>
    <div class="col-md form-group">
        <button class="form-control btn btn-custom addBtn float-end" id="btnAddUser" style="float: left;"><i class="fas fa-plus"></i> ADD NEW</button>
    </div>
</div>
<div class="table-responsive container-fluid pt-2">
    <table id="userTable" class="table userTable table-bordered table-striped table-hover display w-100" style="cursor: pointer; width: 100%;">
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
                    <select class="form-control filter-select form-select" data-column="3" style="border:1px solid #808080">
                        <option value="" selected></option>
                        <option value="SUPERUSER" style="color: Black;">SUPERUSER</option>
                        <option value="ACCOUNTING" style="color: Black;">ACCOUNTING</option>
                        <option value="WAREHOUSE" style="color: Black;">WAREHOUSE</option>
                    </select>
                </td>
                <td>
                    <select class="form-control filter-select form-select" data-column="4" style="border:1px solid #808080">
                        <option value="" selected></option>
                        <option value="ADMIN" style="color: Black;">ADMIN</option>
                        <option value="ENCODER" style="color: Black;">ENCODER</option>
                        <option value="VIEWER" style="color: Black;">VIEWER</option>
                        <option value="BOSS" style="color: Black;" class="superuser">BOSS</option>
                    </select>
                </td>
                <td>
                    <select class="form-control form-select filter-select" data-column="5" style="border:1px solid #808080">
                        <option value="" selected></option>
                        <option value="ACTIVE" style="font-weight: bold; color: #2ab934;">ACTIVE</option>
                        <option value="INACTIVE" style="font-weight: bold; color: #ca2222;">INACTIVE</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>FULL NAME</th>
                <th>EMAIL ADDRESS</th>
                <th>COMPANY</th>
                <th>DEPARTMENT</th>
                <th>USER LEVEL</th>
                <th style="width: 120px;">STATUS</th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
@include('modals/modalUser')
<script src={{asset('js/users.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection