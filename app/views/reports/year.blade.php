@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report',$end->format('Y')))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Report for {{$end->format('Y')}}</small>
        </h1>
        <p class="lead">
            @if($data['totalDiff'] > 0)
                In {{$end->format('Y')}} you have earned {{mf($data['totalDiff'],true)}},
            @elseif($data['totalDiff'] < 0)
                In {{$end->format('Y')}} you have spent <span class="text-danger">{{mf($data['totalDiff']*-1)}}</span>,
            @else
                In {{$end->format('Y')}} you balanced the books (<span style="color:#999">{{mf($data['totalDiff']*-1)}}</span>),
            @endif

            @if($accounts['netWorthDifference'] > 0 && $data['totalDiff'] > 0)
                and increased your net worth to {{mf($accounts['netWorthEnd'],true)}}.
            @endif

            @if($accounts['netWorthDifference'] < 0 && $data['totalDiff'] < 0)
                and decreased your net worth to {{mf($accounts['netWorthEnd'],true)}}
            @endif

            @if($accounts['netWorthDifference'] == 0)
                and kept your net worth equal at {{mf($accounts['netWorthDifference'],true)}}
            @endif
        </p>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-3">
        <h3>Monies</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <td>Earned</td>
                <td style="text-align:right;">{{mf($data['totalEarned'],
                    true)}}</td>
            </tr>
            <tr>
                <td>Spent</td>
                <td style="text-align:right;"><span class="text-danger">{{mf($data['totalSpent']*-1)
                    }}</span></td>
            </tr>
            <tr>
                <td>Balance</td>
                <td style="text-align:right;">{{mf($data['totalDiff'],
                    true)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-5 col-md-5">
        <h3>Net worth</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Account</th>
                <th>{{$start->format('M jS, Y')}}</th>
                <th>{{$end->format('M jS, Y')}}</th>
                <th>Diff</th>
            </tr>
        @foreach($accounts['accounts'] as $a)
        <tr>
            <td>{{{$a->name}}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate($start),false)}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate($end),false)}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate
                ($end)-$a->balanceOnDate($start),false)}}</td>
        </tr>
        @endforeach
            <tr>
                <td>&nbsp;</td>
                <td style="text-align:right;">{{mf($accounts['netWorthStart'],true)}}</td>
                <td style="text-align:right;">{{mf($accounts['netWorthEnd'],true)}}</td>
                <td style="text-align:right;">{{mf($accounts['netWorthDifference'],
                        true)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-3 col-md-3">
        <h3>Avg / month</h3>
        <table class="table table-bordered">
            <tr>
                <td>Spent</td>
                <td style="text-align:right;">{{mf($data['totalSpent']/12,true)}}</td>
            </tr>
            <tr>
                <td>Earned</td>
                <td style="text-align:right;">{{mf($data['totalEarned']/12,true)}}</td>
            </tr>
            </table>

    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div id="netWorth"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-4">
        <h3>Your largest benefactors</h3>
        <div id="report-benefactor-chart"></div>
        <table class="table table-bordered table-striped">
        @foreach($benefactors as $x)
        <tr>
            <td><a href="{{URL::Route('beneficiaryoverview',
            $x['id'])}}" title="{{{$x['name']}}}">{{{$x['name']}}}</a></td>
            <td style="text-align:right;">{{mf($x['sum'],true)}}</td>
        </tr>
        @endforeach
        </table>


    </div>
    <div class="col-lg-4 col-md-4">
        <h3>Your biggest fans</h3>
        <div id="report-fan-chart"></div>
        <table class="table table-bordered table-striped">
            @foreach($fans as $x)
            <tr>
                <td><a href="{{URL::Route('beneficiaryoverview',
                $x['id'])}}" title="{{{$x['name']}}}">{{{$x['name']}}}</a></td>
                <td style="text-align:right;">{{mf($x['sum'],true)}}</td>
            </tr>
            @endforeach
        </table>

    </div>
    <div class="col-lg-4 col-md-4">
        <h3>Most money spent on:</h3>
        <div id="report-cat-chart"></div>
        <table class="table table-bordered table-striped">
            @foreach($spentMostCategories as $x)
            <tr>
                <td><a href="{{URL::Route('categoryoverview',
                $x['id'])}}" title="{{{$x['name']}}}">{{{$x['name']}}}</a></td>
                <td style="text-align:right;">{{mf($x['sum'],true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Allowance comparisions</h4>
    </div>
    </div>

@foreach($allowance as $month)
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <h5>{{$month['date']}}</h5>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h6>Within allowance</h6>
            <table class="table table-condensed table-bordered table-striped">
                <tr>
                    <td colspan="3" style="text-align: right";>Sum: <strong>{{mf($month['inside_sum'],true)}}</strong></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            @foreach($month['inside'] as $entry)
                <tr>
                    <td><small>{{$entry->date->format('D dS')}}</small></td>
                    <td>{{$entry->description}}</td>
                    <td>{{mf($entry->amount,true)}}</td>
                </tr>

            @endforeach
            </table>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h6>Outside of allowance</h6>
            <table class="table table-condensed table-bordered table-striped">
                <tr>
                    <td colspan="3" style="text-align: right";>Sum: <strong>{{mf($month['outside_sum'],true)}}</strong></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
                @foreach($month['outside'] as $entry)
                <tr>
                    <td><small>{{$entry->date->format('D dS')}}</small></td>
                    <td>{{$entry->description}}</td>
                    <td>{{mf($entry->amount,true)}}</td>
                </tr>

                @endforeach
            </table>
        </div>
    </div>
    @endforeach


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Budgets</h3>

    </div>
</div>
@foreach($budgets as $budget)
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>{{$budget->name}}</h4>
        <div class="report-budget-year-chart"
             id="report-budget-year-chart-{{$budget->id}}"
             data-id="{{$budget->id}}"></div>
    </div>
</div>
@endforeach

@endsection
@section('scripts')
<script type="text/javascript">
    var year = {{$end->format('Y')}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/report.js"></script>
@endsection