@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addpiggybank'))
@section('content')
<div class="row">
  <div class="col-lg-12 col-md-12">
    <h3>Create a new piggy bank</h3>
      </div>
    </div>
<div class="row">
    <div class="col-lg-6 col-md-12">


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
                       value="{{{Input::old('name')}}}" id="inputName"
                       placeholder="Name">
                @if($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')}}</span><br />
                @endif
            </div>
        </div>
    
        <!-- Target -->
        <div class="form-group
             @if($errors->has('target'))
             has-error
             @endif
             ">
            <label for="inputTarget" class="col-sm-4 control-label
            help-popover" title="Target" data-content="If you're
            saving up, set a target amount for this piggy bank.">Target</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{Input::old('target')}}"
                           name="target" step="any" class="form-control"
                           id="inputTarget">
                </div>
                @if($errors->has('target'))
                <span class="text-danger">{{$errors->first('target')}}</span>
                @endif
            </div>
        </div>

      <button type="submit" class="btn btn-default">Save new piggy
          bank</button>

    {{Form::close()}}

  </div>
</div>


@stop
@section('scripts')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/accounts.js"></script>
@stop