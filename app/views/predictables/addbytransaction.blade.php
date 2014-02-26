@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addtransaction'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Add a new predictable
        </h3>
    </div>
</div>

{{Form::open(['class' => 'form-horizontal'])}}
<div class="row">

    <div class="col-lg-8">
        <!-- description -->
        <div class="form-group
             @if($errors->has('description'))
             has-error
             @endif
             ">
            <label for="inputDescription" class="col-sm-3 control-label">Expected description</label>
            <div class="col-sm-9">
                <input type="text" name="description" class="form-control"
                       value="{{{Input::old('description') ? Input::old('description') : $transaction->description}}}"
                       id="inputDescription" placeholder="Description">
                @if($errors->has('description'))
                <span class="text-danger">{{$errors->first('description')
                    }}</span><br />
                @endif
            </div>
        </div>
        <!-- expected amount -->
        <div class="form-group
             @if($errors->has('amount'))
             has-error
             @endif
             ">
            <label for="inputAmount" class="col-sm-3 control-label">Expected amount</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{Input::old('amount') ? Input::old('amount') : $transaction->amount}}" name="amount" step="any" class="form-control" id="inputAmount">
                </div>
                <span>There is a X% leeway on this amount to either side.</span>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')
                    }}</span><br />
                @endif
            </div>
        </div>

        <!-- LEEWAY -->
        <div class="form-group">
            <label for="inputPercentage" class="col-sm-3 control-label">Leeway</label>
            <div class="col-sm-9">
                <div class="input-group">

                    <input type="number" value="10" name="pct" max="100" min="0" class="form-control" id="inputPercentage">
                    <span class="input-group-addon">%</span>
                </div>
                <span>There is a X% leeway on this amount to either side.</span>
            </div>
        </div>

        <!-- LEEWAY -->
        <div class="form-group">
            <label for="inputDom" class="col-sm-3 control-label">Day of month</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="number" value="{{$transaction->date->format('j')}}" name="dom" max="31" min="1" class="form-control" id="inputDom">
                    <span>Day of month this usually occurs.</span>
                </div>
            </div>
        </div>

        <!-- expected components -->
        @foreach($transaction->components as $c)
        <div class="form-group">
            <label for="inputComponent{{$c->id}}" class="col-sm-3 control-label">Expected {{$c->type}}</label>
            <div class="col-sm-9">
                <input type="hidden" name="component[]" value="{{$c->id}}" />
                <input type="text" disabled="disabled" class="form-control" value="{{$c->name}}" id="inputComponent{{$c->id}}">
            </div>
        </div>
        @endforeach
    </div>
</div>


<div class="row">
    <div class="col-lg-3 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save new predictable" class="btn btn-default btn-info btn-lg" />
    </div>

</div>

{{Form::close()}}   
@stop
@section('scripts')
<script src="js/typeahead.min.js"></script>
<script src="js/transactions.js"></script>
@stop
@section('styles')
<link href="css/typeahead.js-bootstrap.css" rel="stylesheet" media="screen">
<link href="css/transactions.css" rel="stylesheet" media="screen">
@stop