@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>All transactions</h3>
        <p>
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
        </p>
        <p>
        {{$transactions->links()}}
        </p>
        @include('list.transactions')
        {{$transactions->links()}}
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop