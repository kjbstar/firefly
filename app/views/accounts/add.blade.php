@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addaccount'))
@section('content')
<div class="row">
  <div class="col-lg-12 col-md-12">
      <h2>Add a new account</h2>
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
                       value="{{{Session::get('account')['name']}}}" id="inputName"
                       placeholder="Name">
                @if($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')
                    }}</span><br />
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
                    <span class="input-group-addon">{{$currency}}</span>
                    <input type="number" value="{{{Session::get('account')['openingbalance']}}}" name="openingbalance" step="any"
                           class="form-control" id="inputOpeningbalance">
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
            <label for="inputOpeningbalancedate" class="col-sm-4
            control-label help-popover" title="Opening balance date"
                   data-content="Combined with the opening balance,
                   the date sets the start for managing this account with
                   Firefly.">Opening balance
                date</label>
            <div class="col-sm-8"t>
                <input type="date" value="{{{Session::get('account')['openingbalancedate']}}}" name="openingbalancedate"
                       class="form-control" id="inputOpeningbalancedate">
                @if($errors->has('openingbalancedate'))
                <span class="text-danger">{{$errors->first
                    ('openingbalancedate')}}</span><br />
                @endif
            </div>
        </div>

        <!-- Make this account a shared account. -->
        <div class="form-group">
            <label for="inputShared" class="col-sm-4 control-label">Shared <small>(optional)</small></label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        @if(Session::get('account')['shared'] == true)
                        <input type="checkbox" name="shared" checked="checked" value="1">
                        @else
                        <input type="checkbox" name="shared" value="1">
                        @endif
                        This is a shared account. Expenses paid from this account won't count
                        towards <em>your</em> expenses. Transfers made to this account <em>will</em> count as
                        expenses.
                    </label>
                </div>
            </div>
        </div>
    

     
      <button type="submit" class="btn btn-default">Save new account</button>

    {{Form::close()}}

  </div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
    // fallback for input type date,
    // which Firefox doesn't support.
    yepnope({
        test : Modernizr.inputtypes.date,
        nope : ['//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js','http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.css'],
        complete: function () {
            $('input[type=date]').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }
    });
</script>
@stop
