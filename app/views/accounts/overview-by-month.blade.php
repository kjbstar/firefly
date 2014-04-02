@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('account',$account,$date))
@section('content')
<div class="row">
    <div class="col-lg-6">
        <h2>Overview for {{{$account->name}}}
            in {{{$date->format('F Y')}}}
        </h2>
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
        <div id="account-overview-by-month-chart"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>Transactions & transfers</h4>
        @include('list.mutations')
    </div>
</div>

@stop
@section('scripts')
<script type="text/javascript">
    var id = {{{$account->id}}};
    var month = {{$date->format('m')}};
    var year = {{$date->format('Y')}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/accounts.js"></script>
@stop
@section('styles')
@stop