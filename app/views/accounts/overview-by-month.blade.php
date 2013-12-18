@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-6">
        <h3>Overview for {{$account->name}} for {{$date->format('F Y')}}</h3>
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
        <div id="account-overview-chart-by-month"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>Transactions</h4>
        @include('list.transactions')
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>Transfers related to {{$account->name}}</h4>
        @include('list.transfers')
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
    var id = {{$account->id}};
    var year = {{$date->format('Y')}};
    var month = {{$date->format('n')}};

</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/accounts.js"></script>
@stop
@section('styles')
@stop