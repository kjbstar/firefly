@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_month',$start))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Montly overview</h2>
        <h3>{{$start->format('F Y')}}</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <table class="table">
            <tr>
                <td style="width:25%;">In</td>
                <td style="width:25%;">{{mf($sums['sumIn'],true)}}</td>
                <td style="width:25%;">Net worth ({{$start->format('j F')}})</td>
                <td style="width:25%;">{{mf($netWorth['start'],true)}}</td>
            </tr>
            <tr>
                <td>Out</td>
                <td>{{mf($sums['sumOut'],true)}}</td>
                <td>Net worth ({{$end->format('j F')}})</td>
                <td>{{mf($netWorth['end'],true)}}</td>
            </tr>
            <tr>
                <td>Difference</td>
                <td>{{mf($sums['sumIn']+$sums['sumOut'],true)}}</td>
                <td>Difference</td>
                <td>{{mf($netWorth['end']-$netWorth['start'],true)}}</td>
            </tr>
        </table>
        </div>
    </div>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Transactions (predicted)</h4>
        <table class="table">
            <tr>
                <td colspan="2">Sum</td>
                <td><strong>{{mf($transactions['predictedSum'],true)}}</strong></td>
            </tr>
            @foreach($transactions['predicted'] as $t)
            <tr>
                <td>{{$t->date->format('j F Y')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{{$t->description}}}</a></td>
                <td>{{mf($t->amount,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Incomes</h4>
        <table class="table">
            <tr>
                <td colspan="2">Sum</td>
                <td><strong>{{mf($incomes['sum'],true)}}</strong></td>
            </tr>
            @foreach($incomes['transactions'] as $t)
            <tr>
                <td>{{$t->date->format('j F Y')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{{$t->description}}}</a></td>
                <td>{{mf($t->amount,true)}}</td>
            </tr>
            @endforeach
        </table>
        <p>
            &nbsp;
        </p>
        <p>
            &nbsp;
        </p>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Chart</h4>
        <div id="all-accounts-chart-month"></div>
    </div>
</div>


<div class="row">

    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Transactions (not predicted)</h4>
        <table class="table">
            <tr>
                <td style="width:85%" colspan="2">Sum</td>
                <td><strong>{{mf($transactions['notPredictedSum'],true)}}</strong></td>
            </tr>
            </table>
        @foreach($transactions['notPredicted'] as $group)
            <h5>{{{$group['category']['name']}}}</h5>
            <table class="table">
            @foreach($group['transactions'] as $t)
                <tr>
                    <td style="width:25%;">{{$t->date->format('j F Y')}}</td>
                    <td style="width:60%;"><a href="{{URL::Route('edittransaction',
                    $t->id)}}">{{{$t->description}}}</a></td>
                    <td>{{mf($t->amount,true)}}</td>
                </tr>
                @endforeach
            </table>
        @endforeach
        </table>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12">
    <h4>Components</h4>

    <table class="table">
        @foreach($components as $c)
        <tr>
            <td>{{ucfirst($c->type)}}: {{{$c->name}}}</td>
            <td>{{mf($c->sum,true)}}</td>
        </tr>

        @endforeach
    </table>

    <p>
        <small><em>Add
                @if(isset($components) && count($components) > 0)
                more
                @endif
                components to this report
                by marking them for reports
                on their respective overviews.
            </em></small>
    </p>

    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
    var year = {{$start->format('Y')}};
    var month = {{$start->format('m')}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/reports.js"></script>
@stop