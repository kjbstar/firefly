@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('piggy'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Piggy banks</small>
        </h1>
        <p class="lead">
        @if($balance > 0)
            Left to divide: {{mf($balance,true)}} <br />
        @elseif($balance < 0)
            Too much in piggy banks! Remove {{mf($balance,true)}} <br />
        @endif
        <small>Total target: {{mf($totalTarget)}}</small>
        </p>
        <a href="{{URL::Route('addpiggybank')}}" class="btn btn-default"><span
                class="glyphicon
        glyphicon-plus"></span>Add piggy bank</a>
        </div>
    </div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <ul class="list-group" id="sortable">
    @foreach($piggies as $pig)
            <li class="list-group-item" id="pig-{{$pig->id}}">
                <h4 class="list-group-item-heading">{{{$pig->name}}}
                    <small><br />{{mf($pig->amount)}}
                        @if(!is_null($pig->target))
                        / {{mf($pig->target)}}
                        @endif
                    </small></h4>

            <div class="btn-group pull-right">
                <a href="{{URL::Route('editpiggy',$pig->id)}}" class="btn
            btn-default
            btn-xs"><span
                        class="glyphicon
            glyphicon-pencil"></span></a>

                <a href="{{URL::Route('piggyamount',$pig->id)}}" class="btn
            btn-info btn-xs" data-toggle="modal" data-target="#PopupModal"><span class="glyphicon
            glyphicon-plus-sign"></span> / <span class="glyphicon
            glyphicon-minus-sign"></span></a>

                <a href="{{URL::Route('deletepiggy',$pig->id)}}" class="btn
            btn-danger
             btn-xs"><span class="glyphicon
            glyphicon-trash"></span></a>

            </div>
                <div class="progress progress-striped" style="width:80%;">
                    <div class="progress-bar
                    @if($pig->pctFilled == 100)
                    progress-bar-success
                    @endif
                    " role="progressbar" aria-valuenow="{{$pig->pctFilled}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$pig->pctFilled}}%;">
                        {{$pig->pctFilled}}%
                        <span class="sr-only">{{$pig->pctFilled}}% Complete</span>
                    </div>
                </div>
            </li>

        @endforeach
        </ul>
    </div>
</div>

@stop
@section('styles')
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
@stop
@section('scripts')
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="js/piggy.js"></script>
@stop