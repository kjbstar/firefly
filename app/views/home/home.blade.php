@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>The current state of affairs</small>
        </h1>
            <div class="btn-group">
                <a href="{{URL::Route('addtransaction')}}"
                   class="btn btn-default"><span
                        class="glyphicon glyphicon-plus-sign"></span> Add
                    transaction</a>
                <a href="{{URL::Route('addtransfer')}}"
                   class="btn btn-default"><span
                        class="glyphicon glyphicon-plus-sign"></span> Add
                    transfer</a>
            </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6">
        <h4>Accounts</h4>
    </div>
    <div class="col-lg-6 col-md-6">
        <h4>Transactions</h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3">
        <div id="home-accounts-chart"></div>
    </div>
    <div class="col-lg-3 col-md-3">
        <table class="table table-condensed table-bordered">
            @foreach($accounts as $account)
            <tr>
                <td><a href="{{$account['url']}}">{{$account['name']}}</a
                        ></td>
                <td class="lead" style="text-align:right;">{{mf
                    ($account['current'],true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="col-lg-6 col-md-6">
        <table class="table table-condensed table-bordered">
            @foreach($transactions as $t)
            <tr>
                <td>{{$t->date->format('j F Y')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a>
                </td>
                <td>{{mf($t->amount,true)}}
            </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 col-md-4"><h4>Beneficiaries</h4></div>
    <div class="col-lg-4 col-md-4"><h4>Budgets</h4></div>
    <div class="col-lg-4 col-md-4"><h4>Categories</h4></div>
</div>
<div class="row">
    <div class="col-lg-4 col-md-4">
        <div
            id="home-beneficiaries-piechart"></div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div id="home-budgets-piechart"></div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div
            id="home-categories-piechart"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4">
        <table class="table table-condensed">
            @foreach($beneficiaries as $b)
            <tr
            @if(isset($b['overpct']))
            class="danger"
            @endif
            >
            <td><a href="{{$b['url']}}">{{$b['name']}}</a></td>
            <td>{{mf($b['amount'],true)}}
                </tr>
                @endforeach
        </table>

    </div>
    <div class="col-lg-4 col-md-4">
        @foreach($budgets as $budget)
        <h5><a href="{{$budget['url']}}">{{$budget['name']}}</a></h5>

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
                Limit: {{mf($budget['limit'])}}.
                @endif
                @if(isset($budget['overspent']))
            <span class="text-danger">Spent: {{mf($budget['amount']*-1)}}
                .</span>
                @else
                Spent: {{mf($budget['amount']*-1)}}.
                @endif
            </small>
        </p>
        @endforeach

    </div>
    <div class="col-lg-4 col-md-4">
        <table class="table table-condensed">
            @foreach($categories as $c)
            <tr
            @if(isset($c['overpct']))
            class="danger"
            @endif
            >
            <td><a href="{{$c['url']}}">{{$c['name']}}</a></td>
            <td>{{mf($c['amount'],true)}}
                </tr>
                @endforeach
        </table>

    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12"><h4>Other months</h4>

        @foreach($history as $h)
        <a class="btn btn-info" style="margin:4px;"
           href="{{$h['url']}}">{{$h['title']}}</a>
        @endforeach
    </div>
</div>
<!--

<div class="row">
    <div class="col-lg-6 col-md-6">
        <h3>Accounts</h3>
        <div id="home-accounts-chart"></div>
        <table class="table table-condensed table-striped">
            <tr>
                <th>Account</th>
                <th title="1st of {{$today->format('F')}}">Start of month</th>
                <th title="{{$today->format('jS \of F')}}">Now</th>
                <th>Diff</th>
            </tr>
            @foreach($accounts as $account)
            <tr>
                <td><a href="{{$account['url']}}">{{$account['name']}}</a></td>
                <td>{{mf($account['balance'],true)}}</td>
                <td>{{mf($account['current'],true)}}</td>
                <td>{{mf($account['diff'],true)}}</td>
            </tr>
            @endforeach
        </table>
        <div class="btn-group">
            <div class="btn-group">
            <a href="{{URL::Route('addtransaction')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transaction</a>
            <a href="{{URL::Route('addtransfer')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add transfer</a>
        </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <h3>Budgets</h3>
        <div id="home-budgets-piechart"></div>
        @foreach($budgets as $budget)
        <h4><a href="{{$budget['url']}}">{{$budget['name']}}</a></h4>
        <div class="progress progress-striped">
            @if(isset($budget['overpct']))
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{$budget['overpct']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['overspent']}}%;">
                <span class="sr-only">{{$budget['overpct']}}% overspent</span>
            </div>
            @endif
            @if(isset($budget['spent']))
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{$budget['spent']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['spent']}}%;">
                <span class="sr-only">{{$budget['spent']}}% or something</span>
            </div>
            @endif
            @if(isset($budget['left']))
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$budget['left']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$budget['left']}}%;">
                <span class="sr-only">{{$budget['left']}}% Complete</span>
            </div>
            @endif
        </div>
        <table class="table table-condensed">
            <tr>
                @if($budget['limit'])
                <td>Limit: {{mf($budget['limit'])}}</td>
                @endif
                @if(isset($budget['overspent']))
                <td class="danger">Spent: {{mf($budget['amount'])}}</td>
                @else
                <td>Spent: {{mf($budget['amount'])}}</td>
                @endif
            </tr>
        </table>

        @endforeach

   </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6">
        <h3>Transactions</h3>
        <table class="table table-condensed table-striped">
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            @foreach($transactions as $t)
            <tr>
                <td>{{$t->date->format('j F Y')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a></td>
                <td>{{mf($t->amount,true)}}
            </tr>
            @endforeach
        </table>
    </div>
    <div class="col-lg-6 col-md-6">
        <h3>Transfers</h3>
        <table class="table table-condensed table-striped">
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            @foreach($transfers as $t)
            <tr>
                <td>{{$t->date->format('j F Y')}}</td>
                <td><a href="{{URL::Route('edittransfer',$t->id)}}">{{$t->description}}</a></td>
                <td>{{mf($t->amount,false)}}
            </tr>
            @endforeach
        </table>

    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6">
        <h3>Categories</h3>
        <div id="home-categories-piechart"></div>
        <table class="table table-condensed table-striped">
            <tr>
                <th>Name</th>
                <th>Amount</th>
            </tr>
        @foreach($categories as $c)
        <tr
            @if(isset($c['overpct']))
            class="danger"
            @endif
            >
            <td><a href="{{$c['url']}}">{{$c['name']}}</a></td>
            <td>{{mf($c['amount'],true)}}
        </tr>
        @endforeach
        </table>
    </div>
    <div class="col-lg-6 col-md-6">
        <h3>Beneficiaries</h3>
        <div id="home-beneficiaries-piechart"></div>
        <table class="table table-condensed table-striped">
            <tr>
                <th>Name</th>
                <th>Amount</th>
            </tr>
        @foreach($beneficiaries as $b)
        <tr
            @if(isset($b['overpct']))
            class="danger"
            @endif
            >
            <td><a href="{{$b['url']}}">{{$b['name']}}</a>  </td>
            <td>{{mf($b['amount'],true)}}
        </tr>
        @endforeach
        </table>
    </div>
    <div class="col-lg-12 col-md-12">
        <h3>Other months</h3>
            @foreach($history as $h)
        <a class="label label-primary"
                          href="{{$h['url']}}">{{$h['title']}}</a>
            @endforeach
    </div>
</div>
-->
@endsection
@section('scripts')
<script type="text/javascript">
    var month = {{$today->format('n')}};
    var year = {{$today->format('Y')}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/home.js"></script>
@endsection