@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('reports'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h2>Yearly overview</h2>

        <h3>{{$year}}</h3>
    </div>
</div>


@stop