@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('piggy'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Piggy banks</small>
        </h1>
        @if($balance > 0)
        <p class="lead">
            Left to divide: {{mf($balance,true)}}
        </p>
        @endif
        <a href="{{URL::Route('addpiggybank')}}" class="btn btn-default"><span
                class="glyphicon
        glyphicon-plus"></span>Add piggy bank</a>
        </div>
    </div>

<div class="row">
    @foreach($piggies as $pig)
    <div class="col-lg-3 col-md-6 col-sm-12">
        <h3>{{$pig->name}}<small><br />{{mf($pig->amount)}}
                @if(!is_null($pig->target))
                / {{mf($pig->target)}}
                @endif
            </small></h3>

        <div class="piggybankFill" style="height:{{$pigHeight}}px;max-width:{{$pigWidth}}px;">
            <div class="piggybankEmpty" style="height:{{$pig->drawHeight}}px;
            width:{{$pigWidth}}px;"></div>
            <img src="i/piggy.png" class="piggyBank"
                 height="{{$pigHeight}}"
                />
        </div>
        <div style="height:30px;"></div>
        <div class="btn-group">
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
    </div>
    @endforeach
</div>

@endsection
@section('scripts')
@endsection