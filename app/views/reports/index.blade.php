@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('reports'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Reports</h2>

        <h3>Yearly overview</h3>

        @if(count($years) > 0)
        <ul>
        @foreach($years as $year)
            <li><a href="{{URL::Route('yearreport',$year)}}" title="Report for {{$year}}">Report for {{$year}}</a></li>
        @endforeach
        </ul>
        @endif
    </div>
</div>
@stop