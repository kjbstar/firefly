@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('reports'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Reports</h2>

        <h3>Yearly overview</h3>
        @if(count($years) > 0)
        @foreach($years as $year => $months)
            <h4>{{$year}}</h4>
            <ul>
                <li><a href="{{URL::Route('yearreport',$year)}}" title="Report for {{$year}}">Report for {{$year}}</a></li>
                <ul>
                @foreach($months as $nr => $month)
                    <li><a href="{{URL::Route('monthreport',[$year,$nr])}}" title="Report for {{$month}}, {{$year}}">Report for {{$month}}, {{$year}}</a></li>
                @endforeach
                </ul>
            </ul>
        @endforeach
        @endif


    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <h4>Compare periods with each other</h4>

        <table class="table">
            <tr>
                <th>Compare</th>
                <th colspan="2">With</th>
            </tr>
            <tr>
                <td>
                    @if(count($years) > 0)
                    <select name="year_left" id="year_left" class="form-control">
                    @foreach($years as $year => $months)
                        <option label="{{$year}}" value="{{$year}}">{{$year}}</option>
                    @endforeach
                    </select>
                    @endif
                </td>
                <td>
                    @if(count($years) > 0)
                    <select name="year_right" id="year_right" class="form-control">
                        @foreach($years as $year => $months)
                        <option label="{{$year}}" value="{{$year}}">{{$year}}</option>
                        @endforeach
                    </select>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-default" id="year-compare">Compare</a>
                </td>
            </tr>
            <tr>
                <td>
                    @if(count($years) > 0)
                    <select name="month_left" id="month_left" class="form-control">
                        @foreach($years as $year => $months)
                        @foreach($months as $nr => $month)
                        <option label="{{$month}}, {{$year}}" value="{{$year}}-{{$nr}}">{{$month}}, {{$year}}</option>
                        @endforeach
                        @endforeach
                    </select>
                    @endif
                </td>
                <td>
                    @if(count($years) > 0)
                    <select name="month_right" id="month_right" class="form-control">
                        @foreach($years as $year => $months)
                        @foreach($months as $nr => $month)
                        <option label="{{$month}}, {{$year}}" value="{{$year}}-{{$nr}}">{{$month}}, {{$year}}</option>
                        @endforeach
                        @endforeach
                    </select>
                    @endif
                </td>
                <td>
                    <a href="#" class="btn btn-default" id="month-compare">Compare</a>
                </td>
            </tr>
        </table>
@stop
@section('scripts')
<script src="js/reports.js"></script>
@stop