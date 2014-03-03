@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('reports'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Reports</h2>

        <h3>Yearly overview</h3>
        @if(count($years) > 0)
        @foreach($years as $year)
            <h4>{{$year}}</h4>
            <ul>
                <li><a href="{{URL::Route('yearreport',$year)}}" title="Report for {{$year}}">Report for {{$year}}</a></li>
            </ul>
        @endforeach

        @endif
    </div>
</div>
@stop