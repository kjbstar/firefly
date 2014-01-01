@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('editaccount',$account))
@section('content')
<div class="row">
  <div class="col-lg-6">
    <h3>Edit {{{$account->name}}}</h3>

    {{Form::open(['class' => 'form-horizontal'])}}
    
    <!-- name -->
        <div class="form-group
             @if($errors->has('name'))
             has-error
             @endif
             ">
            <label for="inputName" class="col-sm-4 control-label">Name</label>
            <div class="col-sm-8">
                <input type="text" name="name" class="form-control"
                       value="{{{$account->name}}}" id="inputName"
                       placeholder="{{{$account->name}}}">
                @if($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')}}</span>
                @endif
            </div>
        </div>
    
        <!-- Opening balance -->
        <div class="form-group
             @if($errors->has('openingbalance'))
             has-error
             @endif
             ">
            <label for="inputOpeningbalance" class="col-sm-4 control-label">Opening balance</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{$account->openingbalance}}" name="openingbalance" step="any" class="form-control" id="inputOpeningbalance">
                </div>
                @if($errors->has('openingbalance'))
                <span class="text-danger">{{$errors->first('openingbalance')}}</span>
                @endif
            </div>
        </div>
        
        <!-- Opening balance date -->
        <div class="form-group
             @if($errors->has('openingbalancedate'))
             has-error
             @endif
             ">
            <label for="inputOpeningbalancedate" class="col-sm-4 control-label">Opening balance date</label>
            <div class="col-sm-8"t>
                <input 
                    
                    type="date" value="{{$account->openingbalancedate->format('Y-m-d')}}" name="openingbalancedate" class="form-control" id="inputOpeningbalancedate">
                @if($errors->has('openingbalancedate'))
                <span class="text-danger">{{$errors->first('openingbalancedate')}}</span>
                @endif
            </div>
        </div>
    
        <!-- Opening balance date -->
        <!-- ignore in transactions (default is zero) -->
        <div class="form-group">
            <label for="inputHidden" class="col-sm-4 control-label">Hidden</label>
            <div class="col-sm-8">
                <input 
                    @if($account->hidden)
                    checked="checked"
                    @endif
                    type="checkbox" name="hidden" value="1"> <small>Hides this account.</small>
            </div>
        </div>
    
     

    <div class="form-group">
      <button type="submit" class="btn btn-default">Save account</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop