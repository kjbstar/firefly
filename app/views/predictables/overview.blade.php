@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictable',$predictable))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Overview for {{{$predictable->description}}}</h3>
        <div class="btn-group">
            <a href="{{URL::Route('editpredictable',$predictable->id)}}" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
            <a href="{{URL::Route('deletepredictable',$predictable->id)}}" class="btn btn-default btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
        </div>
        <p>
            "{{{$predictable->description}}}" triggers the {{$predictable->dayOfMonth()}} day of each month
            for amounts between {{mf($predictable->minimumAmount(),true)}} and {{mf($predictable->maximumAmount(),true)}}.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        @if(count($predictable->components) > 0)
        <p>
            Only transactions with
            the following meta-data are detected.
        </p>
        <table class="table">
            @foreach($predictable->components as $component)
            <tr>
                <td>{{ucfirst($component->type->type)}}</td>
                <td><a href="{{URL::Route('componentoverview',$component->id)}}">{{{$component->name}}}</a></td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h4>Transactions</h4>
        @include('list.mutations-large',['mutations' => $transactions])

        <div class="btn-group">
            <a href="{{URL::Route('rescanpredictable',$predictable->id)}}" class="btn btn-default">(re)scan transactions</a>
        </div>
    </div>
</div>


@stop
