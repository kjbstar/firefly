@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('home'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>The current state of affairs</small>
        </h1>

        @if($accountCount > 0)
            <div class="btn-group">
                <a href="{{URL::Route('addtransaction')}}"
                   class="btn btn-default"><span
                        class="glyphicon glyphicon-plus-sign"></span> Add
                    transaction</a>
                @if($accountCount > 1)
                <a href="{{URL::Route('addtransfer')}}"
                   class="btn btn-default"><span
                        class="glyphicon glyphicon-plus-sign"></span> Add
                    transfer</a>
                @endif
            </div>
        @endif
    </div>
</div>
@if($accountCount == 0)
<div class="row">
    <div class="col-lg-6 coll-md-6 col-lg-offset-3 col-md-offset-3">
        <div class="alert alert-info">
            <p>
                <strong>Start with Firefly</strong>
            </p>
            <p>
                In order to start using Firefly, you need to enter at least
                one account. This can be a checking account,
                credit card or savings account. It can even be your cash
                wallet.
            </p>
            <p><a class="alert-link" href="{{URL::Route('addaccount')
            }}">Create an account
                    to
                  continue</a>
                .</p>
        </div>
    </div>
</div>
@endif
@if($transactionCount == 0)
<div class="row">
    <div class="col-lg-6 coll-md-6 col-lg-offset-3 col-md-offset-3">
        <div class="alert alert-info">
            <p>
                <strong>Start with Firefly</strong>
            </p>
            <p>
                Now that you have an account, you can create your first
                transaction. You should start tracking all your expenses and
                incomes so Firefly can accurately reflect your current
                financial standing.
            </p>
            <p><a class="alert-link" href="{{URL::Route('addtransaction')
            }}">Create a transaction
                    to
                    continue</a>
                .</p>
        </div>
    </div>
</div>
@endif

<div class="row">
    @if($accountCount > 0)
    <div class="col-lg-3 col-md-3">
        <h4>Accounts</h4>
        <div id="home-accounts-chart"></div>
    </div>
    <div class="col-lg-3 col-md-3">
        <h4>&nbsp;</h4>
        <table class="table table-condensed table-bordered">
            @foreach($accounts as $account)
            <tr>
                <td><a href="{{$account['url']}}">{{{$account['name']}}}</a
                        ></td>
                <td style="text-align:right;">{{mf
                    ($account['current'],true,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="col-lg-6 col-md-6">
        <h4>General information</h4>
        <ul class="nav nav-tabs">
            @if($allowance['amount'] > 0)
            <li class="active"><a href="#budgeting-tab"
                                  data-toggle="tab">Budgeting</a></li>
            <li><a href="#transactions-tab" data-toggle="tab">Transactions</a></li>
            @else
            <li class="active"><a href="#transactions-tab"
                    data-toggle="tab">Transactions</a></li>
            @endif

            <li><a href="#transfers-tab" data-toggle="tab">Transfers</a></li>
        </ul>
        <div class="tab-content" style="margin-top:10px;">
            @if($allowance['amount'] > 0)
            <div class="tab-pane active" id="budgeting-tab">
                <!-- progress in this month -->
                <div class="progress progress-striped" style="margin-bottom:0;height:10px;">
                    <div class="progress-bar progress-bar-info"
                         role="progressbar"
                         aria-valuenow="{{$allowance['days']}}" aria-valuemin="0"
                         aria-valuemax="100" style="width:
                         {{$allowance['days']}}%;">

                        <span class="sr-only">{{$allowance['days']}}% Complete</span>
                    </div>

                </div>

                <!-- monetary progress -->
                <div class="progress progress-striped">
                    @if($allowance['over'])
                    <div class="progress-bar progress-bar-warning"
                         role="progressbar"
                         aria-valuenow="{{$allowance['pct']}}"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width:
                         {{$allowance['pct']}}%;">
                        <span class="sr-only">{{$allowance['pct']}}% Complete</span>
                    </div>
                    <div class="progress-bar progress-bar-danger"
                         role="progressbar"
                         aria-valuenow="{{$allowance['pct']}}"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width:
                         {{100-$allowance['pct']}}%;">
                        <span class="sr-only">{{$allowance['pct']}}% Complete</span>
                    </div>
                    @else
                    <div class="progress-bar progress-bar-success"
                         role="progressbar"
                         aria-valuenow="{{$allowance['pct']}}"
                         aria-valuemin="0"
                         aria-valuemax="100" style="width:
                         {{$allowance['pct']}}%;">
                        <span class="sr-only">{{$allowance['pct']}}% Complete</span>
                    </div>
                    @endif

                </div>
                &nbsp;&nbsp;{{mf($allowance['amount'])}} - {{mf($allowance['spent'])}}
                = {{mf($allowance['amount'] - $allowance['spent'],true)}}

            </div>
            @endif
            <div class="tab-pane" id="transactions-tab">
                <table class="table table-condensed table-bordered">
                    @foreach($transactions as $t)
                    <tr>
                        <td>
                            <span class="visible-lg visible-md">
                            {{$t->date->format(Config::get('firefly.date_format'))}}
                            </span>
                            <span class="visible-lg visible-sm hidden-md
                            hidden-lg">
                            {{$t->date->format(Config::get('firefly.date_format_small'))}}
                            </span>
                        </td>
                        <td><a href="{{URL::Route('edittransaction',
                $t->id)}}">{{{$t->description}}}</a>
                        </td>
                        <td style="text-align: right;">{{mf($t->amount,true,true)}}
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="tab-pane" id="transfers-tab">
                <table class="table table-condensed table-bordered">
                @foreach($transfers as $t)
                <tr>
                    <td>
                        <span class="visible-lg visible-md">
                            {{$t->date->format(Config::get('firefly.date_format'))}}
                            </span>
                            <span class="visible-lg visible-sm hidden-md
                            hidden-lg">
                            {{$t->date->format(Config::get('firefly.date_format_small'))}}
                            </span>
                    </td>
                    <td><a href="{{URL::Route('edittransfer',
                $t->id)}}">{{{$t->description}}}</a>
                    </td>
                    <td style="text-align: right;">{{mf($t->amount,false,
                        true)}}
                </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="row">
    @if($accountCount > 0 && $transactionCount > 0)
    <div class="col-lg-4 col-md-4">
        <h4>Beneficiaries</h4>
        <div
            id="home-beneficiary-piechart"></div>
        <table class="table table-condensed">
            <?php
            $sum=0;
            ?>
            @foreach($beneficiaries as $b)
            <tr
            @if(isset($b['overpct']))
            class="danger"
            @endif
            >
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
    <div class="col-lg-4 col-md-4">
        <h4>Budgets</h4>
        <div id="home-budget-piechart"></div>

        <?php
        $sum=0;
        ?>
        @foreach($budgets as $budget)
        <h5><a href="{{$budget['url']}}">{{{$budget['name']}}}</a></h5>

        <div class="progress progress-striped" style="height:10px;">
            @if(isset($budget['overpct']))
            <div class="progress-bar progress-bar-danger" role="progressbar"
                 aria-valuenow="{{$budget['overpct']}}" aria-valuemin="0"
                 aria-valuemax="100" style="width: {{$budget['overspent']}}%;">
            </div>
            @endif
            @if(isset($budget['spent']))
            <div class="progress-bar progress-bar-warning" role="progressbar"
                 aria-valuenow="{{$budget['spent']}}" aria-valuemin="0"
                 aria-valuemax="100" style="width: {{$budget['spent']}}%;">
                <span class="sr-only">{{$budget['spent']}}% or something</span>
            </div>
            @endif
            @if(isset($budget['left']))
            <div class="progress-bar progress-bar-success" role="progressbar"
                 aria-valuenow="{{$budget['left']}}" aria-valuemin="0"
                 aria-valuemax="100" style="width: {{$budget['left']}}%;">
                <span class="sr-only">{{$budget['left']}}% Complete</span>
            </div>
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
    <div class="col-lg-4 col-md-4">
        <h4>Categories</h4>
        <div
            id="home-category-piechart"></div>

        <table class="table table-condensed">
            <?php
            $sum=0;
            ?>
            @foreach($categories as $c)
            <tr
            @if(isset($c['overpct']))
            class="danger"
            @endif
            >
            <td><a href="{{$c['url']}}">{{{$c['name']}}}</a></td>
            <td style="text-align: right;">{{mf($c['amount'],true,true)}}
                </tr>
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
    @endif
</div>

<div class="row">
    @if($accountCount > 0)
    <div class="col-lg-12 col-md-12"><h4>Other months</h4>

        @foreach($history as $h)
        <a class="btn btn-info" style="margin:4px;"
           href="{{$h['url']}}">{{$h['title']}}</a>
        @endforeach
    </div>
    @endif
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    var month = {{$today->format('n')}};
    var year = {{$today->format('Y')}};
    @if($allowance['amount'] > 0)
    var colorAllowance = true;
    @endif
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/home.js"></script>
@endsection