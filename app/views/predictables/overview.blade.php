@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictable',$predictable))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Overview for {{$predictable->description}}</h3>
        <p>
            Basic intro about predictable.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        Hier overview van andere properties van dit predictable.
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h4>Transactions</h4>
        @include('list.mutations-large',['mutations' => $transactions])

        <div class="btn-group">
            <a href="{{URL::Route('rescanpredictable',$predictable->id)}}" class="btn btn-default">(re)scan transactions</a>
            <a href="{{URL::Route('rescanallpredictable',$predictable->id)}}" class="btn btn-default">(re)scan ALL transactions</a>
        </div>
    </div>
</div>


@stop
