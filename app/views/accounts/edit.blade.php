@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('editaccount',$account))
@section('content')
<div class="row">
  <div class="col-lg-6">
    <h2>Edit {{{$account->name}}}</h2>

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
                       value="{{{$prefilled['name']}}}" id="inputName"
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
                    <span class="input-group-addon">{{$currency}}</span>
                    <input type="number" value="{{{$prefilled['openingbalance']}}}" name="openingbalance" step="any"
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
            <label for="inputOpeningbalancedate" class="col-sm-4 control-label">Opening balance date</label>
            <div class="col-sm-8"t>
                <input 
                    
                    type="date" value="{{{$prefilled['openingbalancedate']}}}" name="openingbalancedate"
                    class="form-control" id="inputOpeningbalancedate">
                @if($errors->has('openingbalancedate'))
                <span class="text-danger">{{$errors->first('openingbalancedate')}}</span>
                @endif
            </div>
        </div>
    
        <!-- Opening balance date -->
        <!-- ignore in transactions (default is zero) -->
        <div class="form-group">
            <label for="inputInactive" class="col-sm-4 control-label">Inactive
                <small>(optional)</small>
            </label>
            <div class="col-sm-8">
                <div class="checkbox">
                    <label>
                        @if($prefilled['inactive'] == true)
                        <input checked="checked" name="inactive" value="1" type="checkbox">
                        @else
                        <input name="inactive" value="1" type="checkbox">
                        @endif
                        Hides the account from most screens.
                    </label>
                </div>
            </div>
        </div>

      <!-- Make this account a shared account. -->
      <div class="form-group">
          <label for="inputShared" class="col-sm-4 control-label">Shared <small>(optional)</small></label>
          <div class="col-sm-8">
              <div class="checkbox">
                  <label>
                      @if($prefilled['shared'] == true)
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
    
     

    <div class="form-group">
      <button type="submit" class="btn btn-default">Save account</button>
    </div>

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
