@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('accounts'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>All accounts</h2>
        
        <p>
            <a href="{{URL::Route('addaccount')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add new account</a>
        </p>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Name</th>
                <th>Current balance</th>
                <th>&nbsp;</th>
            </tr>
            @foreach($accounts as $a)
            @if($a->hidden == 1)
            <tr class="warning">
            @else
            <tr>
            @endif
                @if($a->hidden == 1)
                <td>{{$a->name}}</td>
                @else
                <td><a href="{{URL::Route('accountoverview',$a->id)}}" title="{{$a->name}}">{{$a->name}}</a></td>
                @endif
                <td>{{mf($a->today,true)}}</td>
                <td>
                    <div class="btn-group">
                        <a href="{{URL::Route('editaccount',$a->id)}}" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a> <a href="{{URL::Route('deleteaccount',[$a->id])}}" class="btn btn-default btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                </td>
            </tr>

            @endforeach
        </table>
        <p>
            <a href="{{URL::Route('addaccount')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add new account</a>
        </p>
        @stop
        @section('scripts')
        @stop
        @section('styles')
        @stop