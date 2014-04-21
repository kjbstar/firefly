@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictables'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>All predictables</h3>
        <p>
            <a href="{{URL::Route('addpredictable')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add predictable</a>
        </p>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Description</th>
                <th>Account</th>
                <th>Required components</th>
                <th colspan="2">Amount</th>
                <th>Day of month</th>
                <th>&nbsp;</th>
            </tr>
            @foreach($predictables as $p)
            @if($p->inactive == 1)
            <tr class="warning">
            @else
            <tr>
            @endif
                <td><a href="{{URL::Route('predictableoverview',$p->id)}}">{{{$p->description}}}</a> <span class="label label-info">{{$p->transactions()->count()}}</span></td>
                <td><a href="{{URL::Route('accountoverview',$p->account_id)}}">{{{$p->account()->first()->name}}}</a></td>
                <td>
                    @foreach($p->components as $c)
                        {{{ucfirst($c->type)}}}<a href="{{URL::Route($c->type.'overview',$c->id)}}">: {{{$c->name}}}</a><br />
                    @endforeach
                </td>
                <td>{{mf($p->amount*(1-($p->pct/100)))}}</td>
                <td>{{mf($p->amount*(1+($p->pct/100)))}}</td>
                <td>{{$p->domDisplay}}</td>
                <td>
                    <div class="btn-group">
                        <a href="{{URL::Route('editpredictable',[$p->id])}}" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="{{URL::Route('deletepredictable',[$p->id])}}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop