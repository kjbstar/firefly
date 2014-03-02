@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('editpiggy',$pig))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Edit piggy bank "{{{$pig->name}}}"
        </h3>
    </div>
</div>

{{Form::open(['class' => 'form-horizontal'])}}
<div class="row">

    <div class="col-lg-6 col-md-6">
        <h4>Mandatory fields</h4>

        <!-- description -->
        <div class="form-group
             @if($errors->has('name'))
             has-error
             @endif
             ">
            <label for="inputName" class="col-sm-3
            control-label">Name</label>
            <div class="col-sm-9">
                <input type="text" name="name" class="form-control"
                       value="{{{Input::old('name') ? Input::old
                       ('name') : $pig->name}}}"
                       id="inputName"
                       placeholder="{{{$pig->name}}}">
                @if($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')}}</span>
                @endif
            </div>
        </div>

        <!-- Amount -->
        <div class="form-group
             @if($errors->has('amount'))
             has-error
             @endif
             ">
            <label for="inputAmount" class="col-sm-3 control-label">Amount</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{Input::old('amount') ?
                    Input::old('amount') : $pig->amount}}" name="amount"
                           step="any" class="form-control" id="inputAmount">
                </div>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <h4>Optional fields</h4>
        <!-- Target -->
        <div class="form-group
             @if($errors->has('target'))
             has-error
             @endif
             ">
            <label for="inputTarget" class="col-sm-3
            control-label">Target</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{Input::old('target') ?
                    Input::old('target') : $pig->target}}" name="target"
                           step="any" class="form-control" id="inputTarget">
                </div>
                @if($errors->has('target'))
                <span class="text-danger">{{$errors->first('target')}}</span>
                @endif
            </div>
        </div>
    </div>

</div>


<div class="row">
    <div class="col-lg-12 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save edits to transaction" class="btn btn-default btn-info btn-lg" />
    </div>
</div>

{{Form::close()}}   
@stop
