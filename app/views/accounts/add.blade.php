@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addaccount'))
@section('content')
<div class="row">
  <div class="col-lg-12 col-md-12">
    <h3>Add a new account</h3>
      @if($count == 0)
      <div class="alert alert-info">
          <p>
              <strong>Your first account</strong>
          </p>
      </div>
      @endif
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
                <span class="text-danger">{{$errors->first('name')
                    }}</span><br />
                @endif
                @if($count == 0)
                <span class="text-info">
                        Use a name that you'll recognize instantly. So no "test" or
                        "first" or something like that. Rather,
                        use something like "[your-bank-name] checking account".
                    </span>
                @endif
            </div>
        </div>
    
        <!-- Opening balance -->
        <div class="form-group
             @if($errors->has('openingbalance'))
             has-error
             @endif
             ">
            <label for="inputOpeningbalance" class="col-sm-4 control-label
            help-popover" title="Opening balance" data-content="Use
            this field to set the 'base' for Firefly to work with.">Opening
                balance</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="number" value="{{Input::old('openingbalance')}}" name="openingbalance" step="any" class="form-control" id="inputOpeningbalance">
                </div>
                @if($errors->has('openingbalance'))
                <span class="text-danger">{{$errors->first('openingbalance')}}</span>
                @endif
                @if($count == 0)
                <span class="text-info">
                        Take a look at your bank statements and find an
                    account's balance and the date of that balance. Fill in
                    the balance here.
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Opening balance date -->
        <div class="form-group
             @if($errors->has('openingbalancedate'))
             has-error
             @endif
             ">
            <label for="inputOpeningbalancedate" class="col-sm-4
            control-label help-popover" title="Opening balance date"
                   data-content="Combined with the opening balance,
                   the date sets the start for managing this account with
                   Firefly.">Opening balance
                date</label>
            <div class="col-sm-8"t>
                <input type="date" value="{{Input::old('openingbalancedate')}}" name="openingbalancedate" class="form-control" id="inputOpeningbalancedate">
                @if($errors->has('openingbalancedate'))
                <span class="text-danger">{{$errors->first
                    ('openingbalancedate')}}</span><br />
                @endif
                @if($count == 0)
                    <span class="text-info">
                        Take a look at your bank statements and find an
                    account's balance and the date of that balance. Fill in
                    the date here.
                    </span>
                @endif
            </div>
        </div>
    

     
      <button type="submit" class="btn btn-default">Save new account</button>

    {{Form::close()}}

  </div>
</div>


@stop
@section('scripts')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="/js/accounts.js"></script>
@stop