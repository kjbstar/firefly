@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('empty',$type,$date))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2>Transactions without a {{$type->type}}

        @if(!is_null($date))
        in {{$date->format('F Y')}}
        @endif
        </h2>
        @include('list.mutations-large')
    </div>
</div>
@stop
@section('scripts')
@stop
@section('styles')
@stop