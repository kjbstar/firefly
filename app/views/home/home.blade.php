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
                <td><a href="{{$account['url']}}">{{{$account['name']}}}</a></td>
                <td style="text-align:right;">{{mf ($account['current'],true,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- GAUGE VOOR HUIDIGE STATE -->
    <div class="col-lg-4 col-md-4 col-sm-12">
        <!-- TOMORROWS PREDICTION. -->
        <div id="gauge-predict-tomorrow" class="gauge"></div>
    </div>

    <!-- GAUGE VOOR EIND VAN DE MAAND STATE -->
    <div class="col-lg-4 col-md-4 col-sm-12">
        <!-- EOMS PREDICTION. -->
        <div id="gauge-predict-eom" class="gauge"></div>
    </div>

</div>


<div class="row">
    <!-- ALL BENEFICIARIES IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-beneficiaries" rel="collapse-objects">
                        Beneficiaries
                    </a>
                </h4>
            </div>
            <div id="collapse-beneficiaries" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
    <!-- ALL BUDGETS IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-budgets" rel="collapse-objects">
                        Budgets
                    </a>
                </h4>
            </div>
            <div id="collapse-budgets" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
    <!-- ALL CATEGORIES IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-categories" rel="collapse-objects">
                        Categories
                    </a>
                </h4>
            </div>
            <div id="collapse-categories" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- TRANSACTIONS IN COLLAPSEABLE -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-transactions" rel="collapse-objects">
                        Transactions
                    </a>
                </h4>
            </div>
            <div id="collapse-transactions" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
    <!-- TRANSFERS IN COLLAPSEABLE -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-transfers" rel="collapse-objects">
                        Transfers
                    </a>
                </h4>
            </div>
            <div id="collapse-transfers" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12 col-sm-12">
        <!-- PREDICTIONS IN COLLAPSEABLE -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-predictions" rel="collapse-objects">
                        Predictions
                    </a>
                </h4>
            </div>
            <div id="collapse-predictions" class="panel-collapse collapse">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12"><h4>Other months</h4>
        @foreach($history as $h)
        <a class="btn btn-info" style="margin:4px;" href="{{$h['url']}}">{{$h['title']}}</a>
        @endforeach
    </div>
</div>
@stop


@section('scripts')
<script type="text/javascript">
    var day = {{$today->format('d')}};
    var month = {{$today->format('n')}};
    var year = {{$today->format('Y')}};
    var tomorrow = "{{$today->addDay()->format('Y/m/d')}}";
    var eom = "{{$today->subDay()->endOfMonth()->format('Y/m/d')}}";

</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/home-settings.js"></script>
<script src="js/home.js"></script>
@stop