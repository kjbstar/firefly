@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_month',$date))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Report <small>{{$date->format('F Y')}}</small></h2>

        </div>
    </div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Account history <small>{{$date->format('F Y')}}</small></h3>
        <div id="report-month"></div>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Summary <small>{{$date->format('F Y')}}</small></h3>

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
        <h3>Expenses breakdown <small>{{$date->format('F Y')}}</small></h3>
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
            <td>{{$m->date->format('jS')}}</td>
            <td>
                @if($m->beneficiary && $m->beneficiary->hasIcon())
                {{$m->beneficiary->iconTag()}}
                @endif

                @if($m->category && $m->category->hasIcon())
                {{$m->category->iconTag()}}
                @endif


                @if($m->budget && $m->budget->hasIcon())
                {{$m->budget->iconTag()}}
                @endif


                <a href="{{URL::Route('edit'.$class,$m->id)}}" title="Edit {{$class}} '{{{$m->description}}}'">{{{$m->description}}}</a></td>
            <td>{{mf($m->amount,true)}}</td>
        </tr>
        @endforeach
            <tr>
                <td colspan="2">&nbsp;</td>
                <td style="width:20%;">{{mf($sum,true)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <h4>Predicted expenses <small>And the actual amount</small></h4>
        <table class="table table-condensed table-bordered table-striped">
            <tr>
                <th>Day</th>
                <th>Description</th>
                <th>Predicted</th>
                <th>Amount</th>
            </tr>
            <?php
            $sum = 0;
            ?>
            @foreach($predicted as $p)
            <?php
            $sum += $p->amount;
            ?>
            <tr>
                <td>{{$p->date->format('jS')}}</td>
                <td>
                    @if($p->beneficiary && $p->beneficiary->hasIcon())
                    {{$p->beneficiary->iconTag()}}
                    @endif

                    @if($p->category && $p->category->hasIcon())
                    {{$p->category->iconTag()}}
                    @endif


                    @if($p->budget && $p->budget->hasIcon())
                    {{$p->budget->iconTag()}}
                    @endif

                    <a href="{{URL::Route('predictableoverview',$p->predictable_id)}}" title="Overview for predictable '{{{$p->description}}}'">{{{$p->description}}}</a></td>
                <td>{{mf($p->predicted,true)}}</td>
                <td>{{mf($p->amount,true)}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3">&nbsp;</td>
                <td style="width:20%;">{{mf($sum,true)}}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    @foreach($expenses as $type => $data)
    <div class="col-lg-4 col-md-6 col-sm-6">
        <h4>All expenses <small>Grouped on {{$type}}</small></h4>

        <div class="pie-chart" id="{{$type}}-piechart" rel="{{$type}}"></div>

        <div class="panel-group" id="accordion-expenses">
            @foreach($data as $row)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-{{$type}}" href="#{{$type}}-{{{Str::slug($row['component']['name'])}}}">
                            @if($row['component']['hasIcon'])
                            {{$row['component']['iconTag']}}
                            @endif
                            {{{$row['component']['name']}}}
                            <span class="pull-right"><small>{{mf($row['component']['sum'],true)}}</small></span>
                        </a>
                    </h5>
                </div>
                <div id="{{$type}}-{{{Str::slug($row['component']['name'])}}}" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-condensed table-bordered table-striped">
                            <tr>
                                <th style="width:15%;">Day</th>
                                <th>Description</th>
                                <th style="width:30%;">Amount</th>
                            </tr>
                            @foreach($row['transactions'] as $t)
                            <tr>
                                <td>{{$t->date->format('jS')}}</td>
                                @if(get_class($t) == 'Transfer')
                                <td><a href="{{URL::Route('edittransfer',$t->id)}}" title="Edit transfer '{{{$t->description}}}'">{{{$t->description}}}</a></td>
                                @else
                                <td><a href="{{URL::Route('edittransaction',$t->id)}}" title="Edit transaction '{{{$t->description}}}'">{{{$t->description}}}</a></td>
                                @endif
                                <td>{{mf($t->amount,true)}}</td>
                            </tr>
                            @endforeach
                            @if(count($row['transactions']) > 1)
                            <tr>
                                <td colspan="2">&nbsp;</td>
                                <td>{{mf($row['component']['sum'],true)}}</td>
                            </tr>
                            @endif
                            </table>
                        </div>
                    </div>
            </div>
            @endforeach
            </div>
    </div>
    @endforeach
    </div>
{{--




        @foreach($categories as $row)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-expenses" href="#category-{{{Str::slug($row['category']['name'])}}}">
                            {{{$row['category']['name']}}}
                            <span class="pull-right"><small>{{mf($row['category']['sum'],true)}}</small></span>
                        </a>
                    </h5>
                </div>
                <div id="category-{{{Str::slug($row['category']['name'])}}}" class="panel-collapse collapse">
                    <div class="panel-body">
            <table class="table table-condensed table-bordered table-striped">
                <tr>
                    <th style="width:15%;">Day</th>
                    <th>Description</th>
                    <th style="width:30%;">Amount</th>
                </tr>
                @foreach($row['transactions'] as $t)
                    <tr>
                        <td>{{$t->date->format('jS')}}</td>
                        <td><a href="{{URL::Route('edittransaction',$t->id)}}" title="Edit transaction '{{{$t->description}}}'">{{{$t->description}}}</a></td>
                        <td>{{mf($t->amount,true)}}</td>
                    </tr>
                @endforeach
                @if(count($row['transactions']) > 1)
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>{{mf($row['category']['sum'],true)}}</td>
                </tr>
                @endif
            </table>
                    </div>
                </div>
                </div>
        @endforeach

        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <h4>All expenses <small>Grouped on budget</small></h4>
        <div class="panel-group" id="accordion-budgets">
        @foreach($budgets as $row)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion-budgets" href="#budget-{{{Str::slug($row['budget']['name'])}}}">
                        {{{$row['budget']['name']}}}
                        <span class="pull-right"><small>{{mf($row['budget']['sum'],true)}}</small></span>
                    </a>
                </h5>
            </div>
            <div id="budget-{{{Str::slug($row['budget']['name'])}}}" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table table-condensed table-bordered table-striped">
                        <tr>
                            <th style="width:15%;">Day</th>
                            <th>Description</th>
                            <th style="width:30%;">Amount</th>
                        </tr>
                        <?php
                        $sum = 0;
                        ?>
                        @foreach($row['transactions'] as $t)
                        <tr>
                            <td>{{$t->date->format('jS')}}</td>
                            <td><a href="{{URL::Route('edittransaction',$t->id)}}" title="Edit transaction '{{{$t->description}}}'">{{{$t->description}}}</a></td>
                            <td>{{mf($t->amount,true)}}</td>
                        </tr>
                        @endforeach
                        @if(count($row['transactions']) > 1)
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>{{mf($row['budget']['sum'],true)}}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        @endforeach
            </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <h4>All expenses <small>Grouped on beneficiary</small></h4>
    </div>

</div>
--}}
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Shared accounts <small>{{$date->format('F Y')}}</small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>Shared accounts</h4>
        @if(count($sharedAccounts) > 0)
        <table class="table table-striped table-bordered table-condensed">
            <tr>
                <th>Account</th>
                <th>Start of {{$date->format('F Y')}}</th>
                <th>End of {{$date->format('F Y')}}</th>
                <th>Diff</th>
            </tr>
            @foreach($sharedAccounts as $entry)
            <tr>
                <td><a href="{{URL::Route('accountoverviewmonth',[$entry['account']->id,$date->format('Y'),$date->format('m')])}}">{{{$entry['account']->name}}}</a></td>
                <td>{{mf($entry['account']->startOfMonth,true)}}</td>
                <td>{{mf($entry['account']->endOfMonth,true)}}</td>
                <td>{{mf($entry['account']->endOfMonth-$entry['account']->startOfMonth,true)}}</td>
            </tr>
            @endforeach
        </table>
        @else
        <p>
            <em>Nothing here</em>
        </p>
        @endif
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        @if(count($sharedAccounts) > 0)
            <h4>Contributions</h4>
            @foreach($sharedAccounts as $account)
                <h5>{{{$entry['account']->name}}}</h5>
                @if(count($entry['contributions']) > 0)
                <table class="table table-condensed table-bordered">
                @foreach($entry['contributions'] as $mutation)
                <tr>
                    <td>
                        <a href="{{URL::Route('componentoverviewmonth',[$mutation->getComponentOfType($entry['payer'])->id,$date->format('Y'),$date->format('m')])}}">
                        {{{$mutation->getComponentOfType($entry['payer'])->name}}}
                        </a>
                    </td>
                    <td>{{mf($mutation->amount,true)}}</td>
                    <td>{{$mutation->pct}}%</td>
                </tr>
                @endforeach
                    <tr>
                        <td style="text-align-right;"><em>Sum</em></td>
                        <td colspan="2">{{mf($entry['contributionsSum'],true)}}</td>
                    </tr>
                </table>
            @else
                <em>Nothing here</em>
            @endif
            @endforeach
        @else
        <p>
            <em>Nothing here</em>
        </p>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>Incomes breakdown <small>{{$date->format('F Y')}}</small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>Incomes <small>Grouped by beneficiary</small></h4>
        @foreach($incomes as $row)
        <h5>{{{$row['beneficiary']['name']}}}</h5>
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
                <td>{{$t->date->format('jS')}}</td>
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
        @endforeach
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/reports.js"></script>
@stop