@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report',$date->format('Y')))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Report for {{$date->format('Y')}}</small>
        </h1>
        <p class="lead">
            @if($data['totalDiff'] > 0)
                In {{$date->format('Y')}} you have earned {{mf($data['totalDiff'],true)}},
            @elseif($data['totalDiff'] < 0)
                In {{$date->format('Y')}} you have spent <span
                class="text-danger">{{mf
            ($data['totalDiff']*-1)}}</span>,
            @else
            In {{$date->format('Y')}} you balanced the books (<span
                style="color:#999">{{mf
            ($data['totalDiff']*-1)}}</span>),
            @endif

            @if($accounts['netWorthDifference'] > 0 && $data['totalDiff'] > 0)
                and increased your net worth to {{mf($accounts['netWorthEnd'],
            true)}}.
            @endif

            @if($accounts['netWorthDifference'] < 0 && $data['totalDiff'] < 0)
                and decreased your net worth to {{mf($accounts['netWorthEnd'],true)}}
            @endif

            @if($accounts['netWorthDifference'] == 0)
            and kept your net worth equal at {{mf
            ($accounts['netWorthDifference'],
            true)}}
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
                <th>Jan 1st</th>
                <th>Dec 31st</th>
                <th>Diff</th>
            </tr>
        @foreach($accounts['accounts'] as $a)
        <tr>
            <td>{{{$a->name}}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate($date),false)}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate($end),false)}}</td>
            <td style="text-align:right;">{{mf($a->balanceOnDate($end)-$a->balanceOnDate($date),false)}}</td>
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
    <div class="col-lg-4">
        <h4>Your largest benefactors</h4>
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
    <div class="col-lg-4">
        <h4>Your biggest fans</h4>
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
    <div class="col-lg-4">
        <h4>Most money spent on:</h4>
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


@endsection
@section('scripts')
<script type="text/javascript">
    var year = {{$date->format('Y')}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/report.js"></script>
@endsection