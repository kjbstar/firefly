@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('transfers'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>All transfers</h3>
        <p>
            <a href="{{URL::Route('addtransfer')}}" class="btn
            btn-default"><span class="glyphicon glyphicon-plus-sign"></span>
                Add transfer</a>
        </p>
        {{$transfers->links()}}
        @include('list.transfers')
        {{$transfers->links()}}
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop