@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('home'))
@section('content')
<!-- TOP BAR -->
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>The current state of affairs</small>
        </h1>
        <div class="btn-group">
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
              <a href="{{URL::Route('addtransfer')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transfer</a>
        </div>
    </div>
</div>


<!-- MAIN ACCOUNT CHART -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Accounts</h3>
        <div id="home-accounts-chart"></div>

    </div>
</div>


<!-- MOST IMPORTANT INFO -->

<div class="row">
    <!-- LIJST MET ACCOUNTS -->
    <div class="col-lg-4 col-md-4 col-sm-12">
        <table class="table table-condensed table-bordered">
            @foreach($accounts as $account)
            <tr>
                @if($account['shared'])
                <td><a href="{{$account['url']}}">{{{$account['name']}}}</a> <img src="i/group.png" alt="Shared account" /></td>
                @else
                <td><a href="{{$account['url']}}">{{{$account['name']}}}</a></td>
                @endif
                <td style="text-align:right;">{{mf ($account['current'],true,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
<!-- ALLOWANCE BAR -->
    <div class="col-lg-8 col-md-12 col-sm-12">
        @if($allowance['amount'] > 0)
        <div class="tab-pane active" id="budgeting-tab">
            <div class="progress progress-striped"style="margin-bottom:0;height:10px;"><div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{$allowance['days']}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$allowance['days']}}%;"><span class="sr-only">{{$allowance['days']}}% Complete</span></div></div>
            <div class="progress progress-striped">
                @if($allowance['over'])
                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{$allowance['pct']}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$allowance['pct']}}%;"><span class="sr-only">{{$allowance['pct']}}% Complete</span></div>
                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{$allowance['pct']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{100-$allowance['pct']}}%;"><span class="sr-only">{{$allowance['pct']}}% Complete</span></div>
                @else
                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$allowance['pct']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$allowance['pct']}}%;"><span class="sr-only">{{$allowance['pct']}}% Complete</span></div>
                @endif
            </div>
            <p>
                {{mf($allowance['amount'])}} - {{mf ($allowance['spent'])}} = {{mf($allowance['amount'] - $allowance['spent'],true)}} </p>
        </div>
        @endif

    </div>
</div>


<div class="row">
    <!-- ALL BUDGETS IN COLLAPSE. -->
    <div class="col-lg-6 col-md-12 col-sm-12">
        @if($fpAccount)
            <h4>Budgets</h4>
            @include('list.budgets-small')
        @endif

    </div>
    <!-- TRANSACTIONS -->
    <div class="col-lg-6 col-md-12 col-sm-12">
        @if($fpAccount)
            <h4>Transactions</h4>
            @include('list.mutations-small',['mutations' => $transactions])
        @endif
    </div>
</div>
<div class="row">
    <!-- TRANSFERS -->
    <div class="col-lg-6 col-md-12 col-sm-12">
        @if($fpAccount)
            <h4>Transfers</h4>
            @include('list.mutations-small',['mutations' => $transfers])
        @endif
    </div>
    <!-- PREDICTABES -->
    <div class="col-lg-6 col-md-12 col-sm-12">
        @if(count($predictables) > 0)
        <h4>Predictables</h4>
        @include('list.predictables-small')
        @endif
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12"><h4>Other months</h4>
        @foreach($history as $h)
        <a class="btn btn-info btn-xs" style="margin:2px;" href="{{$h['url']}}">{{{$h['title']}}}</a>
        @if($h['newline'] === true)
        <br />
        @endif
        @endforeach
    </div>
</div>
@stop


@section('scripts')
<script type="text/javascript">
    var day = {{$today->format('d')}};
    var month = {{$today->format('n')}};
    var year = {{$today->format('Y')}};
    var fpAccount = {{$fpAccount ? $fpAccount->id : 0}};
    var addAccountURL = '{{URL::Route('addaccount')}}';

</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/home-settings.js"></script>
<script src="js/home.js"></script>
@stop