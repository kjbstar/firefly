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
                <td><a href="{{URL::Route('predictableoverview',$p->predictable_id)}}" title="Overview for predictable '{{{$p->description}}}'">{{{$p->description}}}</a></td>
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
    <div class="col-lg-6 col-md-6 col-sm-6">
        <h4>All expenses <small>Grouped on category</small></h4>

        <div class="panel-group" id="accordion-expenses">
        @foreach($expenses as $row)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-expenses" href="#category-{{{Str::slug($row['category']['name'])}}}">
                            {{{$row['category']['name']}}}
                        </a>
                    </h5>
                </div>
                <div id="category-{{{Str::slug($row['category']['name'])}}}" class="panel-collapse collapse">
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
                    </div>
                </div>
                </div>
        @endforeach

        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <h4>Budget breakdown</h4>
        @foreach($budgets as $row)
        @if(is_null($row['budget']))
            <h5>(no budget)</h5>
        @else
            <h5>{{{$row['budget']->name}}}</h5>
        @endif
        <table class="table table-condensed table-bordered table-hover">
            <tr>
                <th>Spent:</th>
                <td style="width:20%;">{{mf($row['amount'],true)}}</td>

            </tr>
            @if(!is_null($row['budget']) && count($row['budget']->limits) == 1)
            <?php
            $left = $row['budget']->limits->first()->amount + $row['amount']
            ?>
                @if($left < 0)
                <tr class="danger">
                    <th>Left:</th>
                    <td style="width:20%;">{{mf($left,true)}}</td>
                </tr>
                @else
                <tr>
                    <th>Left:</th>
                    <td style="width:20%;">{{mf($left,true)}}</td>
                </tr>
                @endif
            @endif
        </table>


        @endforeach
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