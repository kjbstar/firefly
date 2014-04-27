@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('componentoverviewmonth',$component,$date))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2>Overview for {{$component->type->type}} {{{$component->name}}}
            in {{{$date->format('F Y')}}}
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <h4>Transactions & transfers</h4>
        @include('list.mutations')
    </div>
</div>

@stop
@section('scripts')
@stop
@section('styles')
@stop