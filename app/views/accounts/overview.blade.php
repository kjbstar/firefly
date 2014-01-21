@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('account',$account,$date))
@section('content')
<div class="row">
    <div class="col-lg-6">
        <h3>Overview for {{{$account->name}}}
            @if(!is_null($date))
            in {{$date->format('F Y')}}
            @endif
        </h3>
        <div class="btn-group">
            <a href="{{URL::Route('addtransaction',[$account->id])}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
            <a href="{{URL::Route('addtransfer',[$account->id])}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transfer</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <p>

        </p>
        <div id="account-overview-chart"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(is_null($date))
        <h4>Months</h4>
        <table class="table table-bordered">
            <tr>
                <th>Month</th>
                <th>Balance at start of month</th>
                <th>In</th>
                <th>Out</th>
                <th>Difference</th>
                <th>Balance at end of month</th>
            </tr>
            @foreach($transactions as $m)
            <tr
                @if($m['diff'] < 500 && $m['diff'] > 0)
                class="warning"
                @elseif($m['diff'] < 0)
                class="danger"
                @elseif($m['diff'] > 1000)
                class="success"
                @endif
                ><!--  class="success", warning, danger-->
                <td><a href="{{$m['url']}}">{{$m['title']}}</a></td>
                <td>{{mf($m['balance_start'],true,true)}}</td>
                <td>{{mf($m['in'],true,true)}}</td>
                <td>{{mf($m['out'],true,true)}}</td>
                <td>{{mf($m['diff'],true,true)}}</td>
                <td>{{mf($m['balance_end'],true,true)}}</td>
            </tr>
            @endforeach
        </table>
        @else
        <h4>Transactions</h4>

        @include('list.transactions')
        @endif
    </div>
</div>

@if(!is_null($predictions))
<div class="row">
    <div class="col-lg-12">
        <h4>Predictions</h4>
        <table class="table table-bordered">
            <tr>
                <th>Day</th>
                <th>Predicted start balance</th>
                <th>Predicted expenses</th>
                <th># of transactions</th>
                <th>Predicted end balance</th>
            </tr>
            @foreach($predictions as $p)
            <tr>
                <td>{{$p['date']}}</td>
                <td>{{mf($p['balance'],true)}}</td>
                <td>{{mf($p['prediction'],true)}}</td>
                <td>{{count($p['transactions'])}}</td>
                <td>{{mf($p['end-balance'],true)}}</td>
            </tr>
            @endforeach
        </table>
        </div>
    </div>

@endif

@stop
@section('scripts')
<script type="text/javascript">
    var id = {{{$account->id}}};
    @if(!is_null($date))
    var month = {{$date->format('m')}};
    var year = {{$date->format('Y')}};
    @else
    var month = null;
    var year = null;
    @endif
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/accounts.js"></script>
@stop
@section('styles')
@stop