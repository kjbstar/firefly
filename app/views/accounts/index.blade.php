@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('accounts'))
@section('content')
<div class="row">
    <div class="col-lg-8">
        <h3>All accounts</h3>
        
        <p>
            <a href="{{URL::Route('addaccount')}}" class="btn btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Add new account</a>
        </p>

        <ul class="list-group">
            @foreach($accounts as $account)

            <li class="list-group-item">
                <span class="badge">{{mf($account->currentbalance)}}</span>
                <a href="{{URL::Route('accountoverview',array($account->id))}}"
                   @if($account->hidden == 1)
                   class="text-warning"
                   @endif
                   >{{$account->name}}</a>
                <div class="btn-group pull-right">
                    <a href="{{URL::Route('editaccount',[$account->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a> <a href="{{URL::Route('deleteaccount',[$account->id])}}" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>&nbsp;&nbsp;
                </div>
            </li>
            @endforeach
        </ul>
        <p class="well">
            <span class="text-warning">These accounts</span> are hidden and generally not useable.
        <p>
            <a href="{{URL::Route('addaccount')}}" class="btn btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Add new account</a>
        </p>
        @stop
        @section('scripts')
        @stop
        @section('styles')
        @stop