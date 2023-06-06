@extends('layouts.app')
@section('content')
<br>

<script src={{asset('js/index.js?ver=')}}{{\Illuminate\Support\Str::random(50)}}></script>
@endsection