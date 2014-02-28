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
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
            <a href="{{URL::Route('addtransfer')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transfer</a>
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
        <table class="table table-bordered table-striped">
            <tr>
                <th>Month</th>
                <th>Balance at start of month</th>
            </tr>
            @foreach($transactions as $m)
            <tr>
                <td><a href="{{$m['url']}}">{{$m['title']}}</a></td>
                <td>{{mf($m['balance_start'],true,true)}}</td>
            </tr>
            @endforeach
        </table>
        @else
        <h4>Transactions</h4>

        @include('list.transactions')
        @endif
    </div>
</div>

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