@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('empty'.OBJ,$date))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>Transactions without a {{OBJ}}

        @if(!is_null($date))
        in {{$date->format('F Y')}}
        @endif
        </h3>
        @include('list.transactions')
    </div>
</div>
@stop
@section('scripts')
@stop
@section('styles')
@stop