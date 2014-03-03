@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_year',$year))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Yearly overview</h2>
        <h3>{{$year}}</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Summary</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <table class="table">
            <tr>
                <th>&nbsp;</th>
                <th>Start of {{$year}}</th>
                <th>End of {{$year}}</th>
                <th>Difference</th>
            </tr>
            <tr>
                <td>Net worth</td>
                <td>{{mf($startNetWorth,true)}}</td>
                <td>{{mf($endNetWorth,true)}}</td>
                <td>{{mf($endNetWorth-$startNetWorth,true)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <table class="table">
            <tr>
                <th>Income</th>
                <th>Expenses</th>
                <th>Difference</th>
            </tr>
            <tr>
                <td>{{mf($totalIncome,true)}}</td>
                <td>{{mf($totalExpenses,true)}}</td>
                <td>{{mf($totalExpenses+$totalIncome,true)}}</td>
            </tr>
        </table>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Incomes versus expenses</h4>
        <div id="ie"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Records of {{$year}}</h4>
    </div>
</div>
<div class="row">
    @if(count($expenses) > 0)
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h5>Biggest single expenses</h5>
        <table class="table">
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
            @foreach($expenses as $e)
            <tr>
                <td>{{$e->date->format('d-M')}}</td>
                <td>{{$e->description}}</td>
                <td>{{mf($e->amount,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
    @if(count($fans) > 0)
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h5>Your biggest fans<br /><small>Without predictable transactions</small></h5>
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Total amount</th>
            </tr>
            @foreach($fans as $index => $f)
            <tr>
                <td>{{$f->name}}</td>
                <td>{{mf($f->total,true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>

@stop
@section('scripts')
<script type="text/javascript">
    var year = {{$year}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/reports.js"></script>
@stop