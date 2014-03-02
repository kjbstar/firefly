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
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h4>Incomes versus expenses</h4>
        <div id="ie"></div>
    </div>
</div>

@stop
@section('scripts')
<script type="text/javascript">
    var year = {{$year}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/reports.js"></script>
@stop