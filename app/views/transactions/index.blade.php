@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('transactions'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2>All transactions</h2>
        <p>
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
        </p>
        <p>
        {{$transactions->links()}}
        </p>
        @include('list.mutations-large',['mutations' => $transactions])
        {{$transactions->links()}}
        <p>
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
        </p>
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop