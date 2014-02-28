@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('piggy'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>{{$title}}</small>
        </h1>
        <p>
            Bla bla explanation be here. </p>
    </div>
</div>

@foreach($data as $entry)
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>{{$entry['date']}}</h3>
    </div>
</div>
@foreach($entry['accounts'] as $account)
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-12">
        <h4>Prediction for {{$account['name']}}</h4>
    </div>
    @if(count($account['transactions']) > 0)
    <div class="col-lg-9 col-md-8 col-sm-12">
        <h4><small>Based on this</small></h4>
    </div>
    @endif
</div>
@if(count($account['transactions']) > 0)
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-12">
        <table class="table table-bordered table-condensed">
            <tr>
                <td style="width:50%;">Maximum</td>
                <td class="success">{{mf($account['prediction']['most'])}}
                </td>
                </tr>
            <tr>
                <td>Minimum</td>
                <td class="success">{{mf($account['prediction']['least'])}}
                </td>
            </tr>
            <tr>
                <td>Prediction</td>
                <td class="success">{{mf($account['prediction']['prediction'])}}
                </td>
            </tr>
        </table>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
            @foreach($account['transactions'] as $set)
        <table class="table table-bordered table-condensed table-striped">
            <tr>
                <th style="width:20%;">Date</th>
                <th style="width:40%;">Description</th>
                <th style="width:20%;">Amount</th>
                <th style="width:20%;">&nbsp;</th>
            </tr>
            @foreach($set['transactions'] as $date => $t)
            <tr>
                <td>{{$t->date->format('d F Y')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a></td>
                <td>{{mf($t->amount)}}</td>
                @if(is_null($t->predictable))
                <td><a href="{{URL::Route('addpredictable',$t->id)}}" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-repeat"></span> Predictable</a></td>
                @else
                <td><a href="{{URL::Route('predictableoverview',$t->predictable->id)}}">{{$t->predictable->description}}</a></td>
                @endif
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right;">Sum:</td>
                <td colspan="2"><strong>{{mf($set['sum'])}}</strong></td>
            </tr>

            @endforeach
        </table>
        </div>
</div>
@else
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <small>No prediction data available</small>
        </div>
    </div>
@endif
@if(count($account['ignored']) > 0)
<div class="row">
    <div class="col-lg-9 col-lg-offset-3 col-md-8 col-md-offset-4 col-sm-12">
        <h4><small>Ignored this</small></h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-9 col-lg-offset-3 col-md-8 col-md-offset-4 col-sm-12">
        <table class="table table-bordered table-condensed table-striped">
            <tr>
                <th style="width:20%;">Date</th>
                <th style="width:40%;">Description</th>
                <th style="width:20%;">Amount</th>
                <th style="width:20%;">&nbsp;</th>
            </tr>
            @foreach($account['ignored'] as $t)
            <tr>
                <td>{{$t->date->format('d F Y')}}</td>
                <td>{{$t->description}}</td>
                <td>{{mf($t->amount)}}</td>
                <td><a href="#" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-repeat"></span> Predictable</a></td>
            </tr>
            @endforeach
            </table>
    </div>
</div>
@else

@endif

@endforeach
@endforeach

@stop
