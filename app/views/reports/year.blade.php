@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_month',$date))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Report <small>{{$date->format('Y')}}</small></h2>

        </div>
    </div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Account history <small>{{$date->format('Y')}}</small></h3>
        <div id="report-year"></div>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Summary <small>{{$date->format('Y')}}</small></h3>

    </div>
</div>
<div class="row">
    <div class="col-lg-6 5col-md-6 col-sm-6">
        <table class="table table-bordered table-condensed table-striped">
            <tr>
                <td>Income</td>
                <td>{{mf($summary['income']['income'],true)}}</td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td>{{mf($summary['income']['expense'],true)}}</td>
            </tr>
            <tr>
                <td><em>Difference</em></td>
                <td style="width:20%;">{{mf($summary['income']['expense'] + $summary['income']['income'],true)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">

        <table class="table table-bordered table-condensed table-striped">
            <tr>
                <td>Net worth <small>{{$summary['networth']['startdate']->format('jS M')}}</small></td>
                <td>{{mf($summary['networth']['start'],true)}}</td>
            </tr>
            <tr>
                <td>Net worth <small>{{$summary['networth']['enddate']->format('jS M')}}</small></td>
                <td style="width:20%;">{{mf($summary['networth']['end'],true)}}</td>
            </tr>
            <tr>
                <td><em>Difference</em></td>
                <td>{{mf($summary['networth']['end'] - $summary['networth']['start'],true)}}</td>
            </tr>
        </table>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Expenses breakdown <small>{{$date->format('Y')}}</small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <h4>Biggest expenses <small>Except predictables</small></h4>
        <table class="table table-condensed table-bordered table-striped">
            <tr>
                <th>Day</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            <?php
            $sum = 0;
            ?>
        @foreach($biggest as $m)
            <?php
            $class = strtolower(get_class($m));
            $sum += $m->amount;
            ?>
        <tr>
            <td>{{$m->date->format('M jS')}}</td>
            <td><a href="{{URL::Route('edit'.$class,$m->id)}}" title="Edit {{$class}} '{{{$m->description}}}'">{{{$m->description}}}</a></td>
            <td>{{mf($m->amount,true)}}</td>
        </tr>
        @endforeach
            <tr>
                <td colspan="2">&nbsp;</td>
                <td style="width:20%;">{{mf($sum,true)}}</td>
            </tr>
        </table>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>All months</h4>
        <table class="table table-bordered table-condensed table-striped">
            <tr>
                <th>Month</th>
                <th>In</th>
                <th>Out</th>
                <th>Difference</th>
            </tr>
            @foreach($months as $m)
            <tr>
                <td><a href="{{$m['url']}}" title="Full report for {{$m['date']}}">{{$m['date']}}</a></td>
                <td>{{mf($m['in'],true)}}</td>
                <td>{{mf($m['out'],true)}}</td>
                <td>{{mf($m['in'] + $m['out'],true)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Shared accounts <small>{{$date->format('F Y')}}</small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>Shared accounts</h4>

        @if(count($sharedAccounts) > 0)
        <table class="table table-bordered">
            <tr>
                <th>Month</th>
                <th>Start of month</th>
                <th>End of month</th>
                <th>Contributions</th>
            </tr>
            @foreach($sharedAccounts as $entry)

            @if(count($entry['data']) > 0)
            <tr>
                <td>{{$entry['date']->format('F Y')}}</td>
                <!-- the first one, currently dont support more. -->
                <td>{{mf($entry['data'][0]['account']->startOfMonth,true)}}</td>
                <td>{{mf($entry['data'][0]['account']->endOfMonth,true)}}</td>
                <td>
                    @if(count($entry['data'][0]['contributions']) > 0)
                    @foreach($entry['data'][0]['contributions'] as $c)
                        <a href="{{URL::Route('componentoverview',$c->getComponentOfType($entry['data'][0]['payer'])->id)}}" title="Contribution by {{$c->getComponentOfType($entry['data'][0]['payer'])->name}}">{{$c->pct}}%</a>
                    @endforeach
                    @endif
                </td>
            </tr>
            @endif
            @endforeach

        </table>
        @endif

        <p>
            A list of all shared accounts, they're change in this monts
            (+/-) and your share of adding money to it.
        </p>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <p>
            Transfers made on the shared account, grouped by
            budget?
        </p>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Incomes breakdown <small>{{$date->format('Y')}}</small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">

        <div class="panel-group" id="accordion-incomes">

        <h4>Incomes <small>Grouped by beneficiary</small></h4>
        @foreach($incomes as $row)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-incomes" href="#beneficiary-{{{Str::slug($row['beneficiary']['name'])}}}">
                        {{{$row['beneficiary']['name']}}}</a>
                    </h5>
                </div>
                <div id="beneficiary-{{{Str::slug($row['beneficiary']['name'])}}}" class="panel-collapse collapse">
                    <div class="panel-body">
        <table class="table table-condensed table-bordered table-striped">
            <tr>
                <th style="width:15%;">Day</th>
                <th>Description</th>
                <th style="width:20%;">Amount</th>
            </tr>
            <?php
            $sum = 0;
            $count = count($row['transactions']);
            ?>
            @foreach($row['transactions'] as $t)
            <?php
            $sum += $t->amount;
            ?>
            <tr>
                <td>{{$t->date->format('M jS')}}</td>
                <td><a href="{{URL::Route('edittransaction',$t->id)}}" title="Edit transaction '{{{$t->description}}}'">{{{$t->description}}}</a></td>
                <td>{{mf($t->amount,true)}}</td>
            </tr>
            @endforeach
            @if($count > 1)
            <tr>
                <td colspan="2">&nbsp;</td>
                <td>{{mf($sum,true)}}</td>
            </tr>
            @endif
        </table>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/reports.js"></script>
@stop