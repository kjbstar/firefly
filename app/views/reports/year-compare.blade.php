@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('report_compare_year',$dateOne,$dateTwo))
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Report <small>Comparing {{$dateOne->format('Y')}} with {{$dateTwo->format('Y')}}</small></h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
    <h3>Summary <small>{{$dateOne->format('Y')}} / {{$dateTwo->format('Y')}}</small></h3>
    <table class="table table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th>{{$dateOne->format('Y')}}</th>
            <th>{{$dateTwo->format('Y')}}</th>
            <th><em>Difference (pct)</em></th>
        </tr>
        <tr>
            <td>Income</td>
            <td>{{mf($summary[$yearOne]['income']['income'],true)}}</td>
            <td>{{mf($summary[$yearTwo]['income']['income'],true)}}</td>
            <td>{{mf($summary[$yearTwo]['income']['income']-$summary[$yearOne]['income']['income'],true)}}</td>
        </tr>
        <tr>
            <td>Expense</td>
            <td>{{mf($summary[$yearOne]['income']['expense'],true)}}</td>
            <td>{{mf($summary[$yearTwo]['income']['expense'],true)}}</td>
            <td>{{mf(($summary[$yearOne]['income']['expense']-$summary[$yearTwo]['income']['expense'])*-1,true)}}</td>
        </tr>
    </table>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6">
        <h3>&nbsp;</h3>
        <table class="table table-bordered table-striped">
            <tr>
                <th>&nbsp;</th>
                <th>{{$dateOne->format('Y')}}</th>
                <th>{{$dateTwo->format('Y')}}</th>
                <th><em>Difference (pct)</em></th>
            </tr>
            <tr>
                <td>Start of year</td>
                <td>{{mf($summary[$yearOne]['networth']['start'],true)}}</td>
                <td>{{mf($summary[$yearTwo]['networth']['start'],true)}}</td>
                <td>{{mf($summary[$yearTwo]['networth']['start']-$summary[$yearOne]['networth']['start'],true)}}</td>
            </tr>
            <tr>
                <td>End of year</td>
                <td>{{mf($summary[$yearOne]['networth']['end'],true)}}</td>
                <td>{{mf($summary[$yearTwo]['networth']['end'],true)}}</td>
                <td>{{mf($summary[$yearTwo]['networth']['end']-$summary[$yearOne]['networth']['end'],true)}}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th rowspan="2">Month</th>
                <th colspan="3">{{$yearOne}}</th>
                <th colspan="3">{{$yearTwo}}</th>
                <th rowspan="2">Total diff</th>
            </tr>
            <tr>
                <th>Income</th>
                <th>Expense</th>
                <th>Difference</th>
                <th>Income</th>
                <th>Expense</th>
                <th>Difference</th>

            </tr>
            @foreach($months as $month => $data)
            <tr>
                <td>{{$month}}

                </td>
                <td><a href="{{$data[$yearOne]['url']}}">{{mf($data[$yearOne]['in'],true)}}</a></td>
                <td>{{mf($data[$yearOne]['out'],true)}}</td>
                <td>{{mf($data[$yearOne]['in']+$data[$yearOne]['out'],true)}}</td>
                <td><a href="{{$data[$yearTwo]['url']}}">{{mf($data[$yearTwo]['in'],true)}}</a></td>
                <td>{{mf($data[$yearTwo]['out'],true)}}</td>
                <td>{{mf($data[$yearTwo]['in']+$data[$yearTwo]['out'],true)}}</td>
                <td>
                    {{
                    mf((($data[$yearOne]['in']+$data[$yearOne]['out'])+
                    ($data[$yearTwo]['in']+$data[$yearTwo]['out']))*-1,true)

                    }}
                </td>
            </tr>
            @endforeach

        </table>
    </div>
</div>
@stop