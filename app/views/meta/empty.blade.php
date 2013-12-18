@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>Transactions without a {{OBJ}}</h3>
        @include('list.transactions')
    </div>
</div>
@stop
@section('scripts')
@stop
@section('styles')
@stop