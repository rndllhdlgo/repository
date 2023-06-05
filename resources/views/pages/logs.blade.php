@extends('layouts.app')
@section('content')
<br>
<div class="row">
    <div class="col">
        <div id="page-name"><h4><span class="page-reload">USER ACTIVITY LOGS</span></h4></div>
    </div>
</div>
<div class="table-responsive container-fluid pt-2">
    <table id="userlogsTable" class="table userlogsTable table-hover table-bordered table-fixed display" style="cursor: pointer; width: 100%;">
        <thead class="bg-default">
            <tr>
                <td>
                    <input type="search" class="form-control filter-input" data-column="0" style="border:1px solid #808080"/>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="2" style="border:1px solid #808080"/>
                </td>
                <td>
                    <select class="form-control filter-select form-select" data-column="3" style="border:1px solid #808080">
                        <option value="" selected></option>
                        @foreach($role as $roles)
                            <option value="{{strtoupper($roles->name)}}">{{strtoupper($roles->name)}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="search" class="form-control filter-input" data-column="4" style="border:1px solid #808080"/>
                </td>
            </tr>
            <tr>
                <th>DATE & TIME</th>
                <th>FULL NAME</th>
                <th>USER LEVEL</th>
                <th>ACTIVITY</th>
            </tr>
        </thead>
    </table>
    <br>
</div>
<hr>
<script src={{asset('js/logs.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection