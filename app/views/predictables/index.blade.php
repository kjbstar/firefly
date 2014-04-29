@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictables'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>All predictables</h3>
        <p>
            <a href="{{URL::Route('addpredictable')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add predictable</a>
        </p>
        @include('list.predictables-large')
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop