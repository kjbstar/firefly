@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('account',$account))
@section('content')
<div class="row">
    <div class="col-lg-6">
        <h2>Overview for account "{{{$account->name}}}"</h2>
        <div class="btn-group">
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
            <a href="{{URL::Route('addtransfer')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transfer</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="account-overview-chart"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <h4>List of months</h4>
            <table class="table table-bordered table-striped">
            <tr>
                <th>Month</th>
                <th>Balance at start of month</th>
            </tr>
            @foreach($months as $m)
                <tr>
                    <td><a href="{{$m['url']}}">{{{$m['title']}}}</a></td>
                    <td>{{mf($m['balance'],true,true)}}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

@stop
@section('scripts')
<script type="text/javascript">
    var id = {{{$account->id}}};
    var month = null;
    var year = null;
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/accounts.js"></script>
@stop
@section('styles')
@stop