@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render(OBJ,$component,$date))
@section('content')
<div class="row">
    <div class="col-lg-6">
        <h2>Overview for {{OBJ}} {{{$component->name}}}
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