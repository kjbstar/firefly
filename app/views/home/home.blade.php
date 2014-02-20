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
        <div id="gauge-predict-tomorrow"></div>
    </div>

    <!-- GAUGE VOOR EIND VAN DE MAAND STATE -->
    <div class="col-lg-4 col-md-4 col-sm-12">
        <img src="http://placehold.it/200x200" alt="Gauge" />
    </div>

</div>


<div class="row">
    <!-- ALL BENEFICIARIES IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        ben
    </div>
    <!-- ALL BUDGETS IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        bud
    </div>
    <!-- ALL CATEGORIES IN COLLAPSE. -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        cat
    </div>
</div>

<div class="row">
    <!-- TRANSACTIONS IN COLLAPSEABLE -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        trans
    </div>
    <!-- TRANSFERS IN COLLAPSEABLE -->
    <div class="col-lg-4 col-md-12 col-sm-12">
        transF
    </div>
    <div class="col-lg-4 col-md-12 col-sm-12">
        <!-- PREDICTIONS IN COLLAPSEABLE -->
        prediction
    </div>
</div>






<!--

<div class="row">

<div class="col-lg-4 col-md-6 col-sm-12">
    <h3>Balance</h3>
    <table class="table table-condensed table-bordered">
        @foreach($accounts as $account)
        <tr>
            <td><a href="{{$account['url']}}">{{{$account['name']}}}</a></td>
            <td style="text-align:right;">{{mf ($account['current'],true,true)}}</td>
        </tr>
        @endforeach
    </table>
</div>

<div class="col-lg-8 col-lg-offset-0 col-md-6 col-sm-12">
    <h3>General information</h3>
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
    <div class="col-lg-8 col-lg-offset-4">
        <div id="gauge_prediction"></div>
    </div>
</div>


<div class="row">
    <div class="col-lg-4 col-md-4">
        <h4>Beneficiaries</h4>

        <div id="home-beneficiary-piechart"></div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style="text-align:right;">
                    <a data-toggle="collapse" href="#collapse-beneficiaries">
                        <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                    </a>
                </h4>
            </div>
            <div id="collapse-beneficiaries" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table table-condensed">
                        <?php
                        $sum = 0;
                        ?>
                        @foreach($beneficiaries as $b)
                        <tr @if(isset($b['overpct'])) class="danger" @endif >
                        <td><a href="{{$b['url']}}">{{{$b['name']}}}</a></td>
                        <td style="text-align: right;">{{mf($b['amount'],true,true)}}</td>
                        </tr>
                        <?php
                        $sum += $b['amount'];
                        ?>
                        @endforeach
                        <tr>
                            <td><em>Total</em></td>
                            <td style="text-align: right;">{{mf($sum,true,true)}}
                        </tr>
                    </table>
                </div>
            </div>
        </div>





    </div>
    <div class="col-lg-4 col-md-4">
        <h4>Budgets</h4>

        <div id="home-budget-piechart"></div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style="text-align:right;">
                    <a data-toggle="collapse" href="#collapse-budgets">
                        <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                    </a>
                </h4>
            </div>
            <div id="collapse-budgets" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    $sum = 0;
                    ?>
                    @foreach($budgets as $budget)
                    <h5><a href="{{$budget['url']}}">{{{$budget['name']}}}</a></h5>

                    <div class="progress progress-striped" style="height:10px;">
                        @if(isset($budget['overpct']))
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{$budget['overpct']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['overspent']}}%;"></div>
                        @endif
                        @if(isset($budget['spent']))
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{$budget['spent']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['spent']}}%;"> <span class="sr-only">{{$budget['spent']}}% or something</span></div>
                        @endif
                        @if(isset($budget['left']))
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$budget['left']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['left']}}%;"><span class="sr-only">{{$budget['left']}}% Complete</span></div>
                        @endif
                    </div>
                    <p>
                        <small>
                            @if($budget['limit'])
                            Limit: {{mf($budget['limit'],false,false)}}.
                            @endif
                            @if(isset($budget['overspent']))
                            <span class="text-danger">Spent: {{mf($budget['amount']*-1,false,false)}}.</span>
                            @else
                            Spent: {{mf($budget['amount']*-1,false,false)}}.
                            @endif
                        </small>
                    </p>
                    <?php
                    $sum += $budget['amount'];
                    ?>
                    @endforeach
                    <table class="table table-condensed">
                        <tr>
                            <td><em>Total</em></td>
                            <td style="text-align: right;">{{mf($sum,true,true)}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>



    </div>
    <div class="col-lg-4 col-md-4">
        <h4>Categories</h4>

        <div id="home-category-piechart"></div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style="text-align:right;">
                    <a data-toggle="collapse" href="#collapse-beneficiaries">
                        <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                    </a>
                </h4>
            </div>
            <div id="collapse-beneficiaries" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table table-condensed">
                        <?php
                        $sum = 0;
                        ?>
                        @foreach($categories as $c)
                        <tr @if(isset($c['overpct'])) class="danger" @endif >
                        <td><a href="{{$c['url']}}">{{{$c['name']}}}</a></td>
                        <td style="text-align: right;">{{mf($c['amount'],true,true)}}</tr>
                            <?php
                            $sum += $c['amount'];
                            ?>
                            @endforeach
                            <tr>
                                <td><em>Total</em></td>
                                <td style="text-align: right;">{{mf($sum,true,true)}}</td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>


    </div>
</div>
-->

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
    var month = {{$today->format('n')}};
    var year = {{$today->format('Y')}};
    //@if ($allowance['amount'] > 0)
    //    var colorAllowance = true;
    //@endif
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/home-settings.js"></script>
<script src="js/home.js"></script>
@stop