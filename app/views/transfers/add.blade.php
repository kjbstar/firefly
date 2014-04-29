@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addtransfer'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Add a new transfer
        </h3>
    </div>
</div>

{{Form::open(['class' => 'form-horizontal'])}}
<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>Mandatory fields</h4>

        <!-- description -->
        <div class="form-group
             @if($errors->has('description'))
             has-error
             @endif
             ">
            <label for="inputDescription" class="col-sm-3 control-label">Description</label>
            <div class="col-sm-9">
                <input type="text" name="description" class="form-control"
                       value="{{{$prefilled['description']}}}"
                       id="inputDescription" placeholder="Description">
                @if($errors->has('description'))
                <span class="text-danger">{{$errors->first('description')}}</span>
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
                    <input type="number" value="{{{$prefilled['amount']}}}" name="amount" step="any"
                           class="form-control" id="inputAmount">
                </div>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')}}</span>
                @endif
            </div>
        </div>

        <!-- Date -->
        <div class="form-group
             @if($errors->has('date'))
             has-error
             @endif
             ">
            <label for="inputDate" class="col-sm-3 control-label">Date</label>
            <div class="col-sm-9">
                <input type="date" name="date" value="{{{$prefilled['date']}}}"  class="form-control" id="inputDate">
                @if($errors->has('date'))
                <span class="text-danger">{{$errors->first('date')}}</span>
                @endif
            </div>
        </div>

        <!-- accounts -->
        <div class="form-group
             @if($errors->has('accountfrom_id'))
             has-error
             @endif
             ">
            <label for="inputAccountfrom" class="col-sm-3 control-label">Account from</label>
            <div class="col-sm-9">
                {{Form::select('accountfrom_id',$accounts,$prefilled['accountfrom_id'],['class' => 'form-control'])}}
                @if($errors->has('accountfrom_id'))
                <span class="text-danger">{{$errors->first('accountfrom_id')}}</span>
                @endif
            </div>
        </div>
        
        <div class="form-group
             @if($errors->has('accountto_id'))
             has-error
             @endif
             ">
            <label for="inputAccountto" class="col-sm-3 control-label">Account to</label>
            <div class="col-sm-9">
                {{Form::select('accountto_id',$accounts,$prefilled['accountto_id'],['class' => 'form-control'])}}
                @if($errors->has('accountto_id'))
                <span class="text-danger">{{$errors->first('accountto_id')}}</span>
                @endif
            </div>
        </div>


    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h4>Optional fields</h4>

        <p class="text-info">
            Adding these properties to a transfer is pretty pointless, unless
            you transfer funds to a shared account. Since money transfered to a shared
            account is considered an expense, such transfers <em>will</em> show up in various
            reports as being an expense. As such, they will need this optional information
            to be properly grouped.
        </p>

        <!-- all component types in a loop! -->
        @foreach(Type::allTypes() as $type)
        <div class="form-group">
            <label for="input{{$type->type}}" class="col-sm-3 control-label">{{ucfirst($type->type)}}</label>
            <div class="col-sm-9">
                <input type="text" value="{{{$prefilled[$type->type]}}}"
                       name="{{$type->type}}" class="form-control" id="input{{$type->type}}" autocomplete="off" />
            </div>
        </div>
        @endforeach

        <!-- ignore in allowance (default is zero) -->
        <div class="form-group">
            <label for="inputIgnoreAllowance" class="col-sm-3
            control-label">Allowance</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        @if($prefilled['ignoreallowance'])
                        <input type="checkbox" name="ignoreallowance" value="1" checked="checked" />
                        @else
                        <input type="checkbox" name="ignoreallowance" value="1" />
                        @endif
                        Do not substract this transaction
                        from the allowance (if set).</label></div>
            </div>
        </div>

    </div>

</div>


<div class="row">
    <div class="col-lg-12 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save new transfer" class="btn btn-default btn-info btn-lg" />
    </div>
</div>

{{Form::close()}}   
@stop
@section('scripts')
<script src="js/typeahead.min.js"></script>
<script src="js/transfers.js"></script>
@stop
@section('styles')
<link href="css/typeahead.js-bootstrap.css" rel="stylesheet" media="screen">
<link href="css/transactions.css" rel="stylesheet" media="screen">
@stop