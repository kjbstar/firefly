@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('transfers'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2>All transfers</h2>
        <p>
            <a href="{{URL::Route('addtransfer')}}" class="btn
            btn-default"><span class="glyphicon glyphicon-plus-sign"></span>
                Add transfer</a>
        </p>
        {{$transfers->links()}}
        @include('list.mutations-large',['mutations' => $transfers])
        {{$transfers->links()}}
        <p>
            <a href="{{URL::Route('addtransfer')}}" class="btn
            btn-default"><span class="glyphicon glyphicon-plus-sign"></span>
                Add transfer</a>
        </p>
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop