@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictable',$predictable))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Overview for {{$predictable->description}}</h3>
        <p>
            Predictable "{{$predictable->description}}" triggers on amounts between
        {{mf($predictable->amount*(1-($predictable->pct/100)),true)}}
         and {{mf($predictable->amount*(1+($predictable->pct/100)),true)}} and will occur on or around the {{$predictable->domDisplay}}.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <table class="table">
            <tr>
                <td>Account</td>
                <td><a href="{{URL::Route('accountoverview',$predictable->account_id)}}">{{{$predictable->account()->first()->name}}}</a></td>
            </tr>
            @if($predictable->components()->count() > 0)
            @foreach($predictable->components()->get() as $c)
            <tr>
                <td>{{ucfirst($c->type)}}</td>
                <td><a href="{{URL::Route($c->type.'overview',$c->id)}}">{{$c->name}}</a></td>
            </tr>
            @endforeach
            @endif
        </table>
        <p>
            <div class="btn-group">
                <a href="{{URL::Route('editpredictable',[$predictable->id])}}" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                <a href="{{URL::Route('deletepredictable',[$predictable->id])}}" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete</a>
            </div>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h4>Transactions</h4>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Components</th>
            </tr>
        @foreach($predictable->transactions()->get() as $t)
        <tr>
            <td>{{$t->date->format('d M Y')}}</td>
            <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{{$t->description}}}</a></td>
            <td>{{mf($t->amount,true)}}</td>
            <td>
                @foreach($t->components as $c)
                {{ucfirst($c->type)}}: <a href="{{URL::Route($c->type.'overview',$c->id)}}">{{{$c->name}}}</a><br />
                @endforeach
            </td>
        </tr>
        @endforeach
        </table>
        <div class="btn-group">
            <a href="{{URL::Route('rescanpredictable',$predictable->id)}}" class="btn btn-default">(re)scan transactions</a>
            <a href="{{URL::Route('rescanallpredictable',$predictable->id)}}" class="btn btn-default">(re)scan ALL transactions</a>
        </div>
        <p>
            <small>
                The second button will also (re)consider transactions
                that are already part of (another / this) predictable.

            </small>
        </p>


    </div>
</div>


@stop
