@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_compare_month',$one,$two))
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Comparision</h2>
        <h3>{{$one->format('F Y')}} with {{$two->format('F Y')}}</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <table class="table">
            <tr>
                <th>&nbsp;</th>
                <th>{{$one->format('F Y')}}</th>
                <th>{{$two->format('F Y')}}</th>
                <th>Difference</th>
                <th>&nbsp;</th>
                <th>{{$one->format('F Y')}}</th>
                <th>{{$two->format('F Y')}}</th>
                <th>Difference</th>
            </tr>
            <tr>
                <td>In</td>
                <td>{{mf($numbers['one']['in'],true)}}</td>
                <td>{{mf($numbers['two']['in'],true)}}</td>
                <td>{{mf($numbers['two']['in']-$numbers['one']['in'],true)}}</td>
                <td>Net worth (start)</td>
                <td>{{mf($numbers['one']['net_start'],true)}}</td>
                <td>{{mf($numbers['two']['net_start'],true)}}</td>
                <td>{{mf($numbers['two']['net_start']-$numbers['one']['net_start'],true)}}</td>
            </tr>
            <tr>
                <td>Out</td>
                <td>{{mf($numbers['one']['out'],true)}}</td>
                <td>{{mf($numbers['two']['out'],true)}}</td>
                <td>{{mf($numbers['two']['out']-$numbers['one']['out'],true)}}</td>
                <td>Net worth (end)</td>
                <td>{{mf($numbers['one']['net_end'],true)}}</td>
                <td>{{mf($numbers['two']['net_end'],true)}}</td>
                <td>{{mf($numbers['two']['net_end']-$numbers['one']['net_end'],true)}}</td>
            </tr>
            <tr>
                <td>Difference</td>
                <td>{{mf($numbers['one']['in']+$numbers['one']['out'],true)}}</td>
                <td>{{mf($numbers['two']['in']+$numbers['two']['out'],true)}}</td>
                <td>&nbsp;</td>
                <td>Difference</td>
                <td>{{mf($numbers['one']['net_end']-$numbers['one']['net_start'],true)}}</td>
                <td>{{mf($numbers['two']['net_end']-$numbers['two']['net_start'],true)}}</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </div>
    </div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Chart</h4>
        <div id="all-accounts-compare-chart-month"></div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Planned expenses</h4>
        <table class="table">
            <tr>
                <th></th>
                <th>{{$one->format('F Y')}}</th>
                <th>{{$two->format('F Y')}}</th>
            </tr>
            <tr>
                <td><em>Sum</em></td>
                <td><strong>{{mf($predictables['sum_one'],true)}}</strong></td>
                <td><strong>{{mf($predictables['sum_two'],true)}}</strong></td>
            </tr>
            @foreach($predictables['predictables'] as $p)
            <tr>
                <td><a href="{{URL::Route('predictableoverview',$p['id'])}}">{{$p['description']}}</a></td>
                <td>
                    @if(!is_null($p['one']))
                        {{mf($p['one']->amount,true)}}
                    @else
                        &dash;
                    @endif
                </td>
                <td>
                    @if(!is_null($p['two']))
                        {{mf($p['two']->amount,true)}}
                    @else
                        &dash;
                    @endif
                </td>
            </tr>
            @endforeach

        </table>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Incomes</h4>
        <table class="table">
            <tr>
                <th>&nbsp;</th>
                <th>{{$one->format('F Y')}}</th>
                <th>{{$two->format('F Y')}}</th>
            </tr>
            <tr>
                <td><em>Sum</em></td>
                <td><strong>{{mf($incomes['one_sum'],true)}}</strong></td>
                <td><strong>{{mf($incomes['two_sum'],true)}}</strong></td>
            </tr>
            @foreach($incomes['incomes'] as $description => $data)
            <tr>
                <td>{{$description}}</td>
                <td>
                    @if(isset($data['one']))
                        {{mf($data['one']['amount'],true)}}
                    @else
                        &dash;
                    @endif
                    </td>
                <td>
                    @if(isset($data['two']))
                        {{mf($data['two']['amount'],true)}}
                    @else
                        &dash;
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-1">
    <h4>Components</h4>
    <table class="table">
        <tr>
            <th></th>
            <th>{{$one->format('F Y')}}</th>
            <th>{{$two->format('F Y')}}</th>
        </tr>
        @foreach($components as $c)
        <tr>
            <td><a href="{{URL::Route($c['component']->type.'overview')}}" title="Overview of all {{Str::plural($c['component']->type)}}">{{ucfirst($c['component']->type)}}</a>: <a href="{{URL::Route($c['component']->type.'overview',$c['component']->id)}}" title="Overview for {{$c['component']->type}} '{{$c['component']->name}}'">{{$c['component']->name}}</td>
            <td>{{mf($c['one'],true)}}</td>
            <td>{{mf($c['two'],true)}}</td>
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

{{--

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
            <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a></td>
            <td>{{mf($t->amount,true)}}</td>
        </tr>
        @endforeach
        </table>

    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Transactions (not predicted)</h4>
        <table class="table">
            <tr>
                <td colspan="2">Sum</td>
                <td><strong>{{mf($transactions['notPredictedSum'],true)}}</strong></td>
            </tr>
        @foreach($transactions['notPredicted'] as $t)
        <tr>
            <td>{{$t->date->format('j F Y')}}</td>
            <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a></td>
            <td>{{mf($t->amount,true)}}</td>
        </tr>
        @endforeach
        </table>
    </div>
</div>
<div class="row">
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
                <td><a href="{{URL::Route('edittransaction',$t->id)}}">{{$t->description}}</a></td>
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
--}}
@stop
@section('scripts')
<script type="text/javascript">
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/reports.js"></script>
@stop