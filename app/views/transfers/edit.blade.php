@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('edittransfer',$transfer))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Edit transfer {{{$transfer->description}}}
        </h3>
    </div>
</div>

{{Form::open(['class' => 'form-horizontal'])}}
<div class="row">

    <div class="col-lg-6">
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
                       value="{{{Input::old('description') ? Input::old
                       ('description') : $transfer->description}}}" id="inputDescription" placeholder="Description">
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
                    <input type="number" value="{{{Input::old('amount') ? Input::old('amount') : $transfer->amount}}}" name="amount" step="any" class="form-control" id="inputAmount">
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
                <input type="date" name="date" value="{{{Input::old('date') ? Input::old('date') : $transfer->date->format('Y-m-d')}}}"  class="form-control" id="inputDate">
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
                {{Form::select('accountfrom_id',$accounts, Input::old('accountfrom_id') ? Input::old('accountfrom_id') : $transfer->accountfrom_id,['class' => 'form-control'])}}
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
                {{Form::select('accountto_id',$accounts, Input::old('accountto_id') ? Input::old('accountto_id') : $transfer->accountto_id,['class' => 'form-control'])}}
                @if($errors->has('accountto_id'))
                <span class="text-danger">{{$errors->first('accountto_id')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <h4>Optional fields</h4>

        <!-- beneficiary (can be created) -->
        <div class="form-group">
            <label for="inputBeneficiary" class="col-sm-3 control-label">Beneficiary</label>
            <div class="col-sm-9">
                <input type="text" value="{{{$prefilled['beneficiary']}}}"
                       name="beneficiary" class="form-control" id="inputBeneficiary" autocomplete="off" />
            </div>
        </div>

        <!-- category (can be created) -->
        <div class="form-group">
            <label for="inputCategory" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9">
                <input type="text" value="{{{$prefilled['category']}}}"
                       name="category" class="form-control" id="inputCategory" autocomplete="off" />
            </div>
        </div>

        <!-- budget (can be created) -->
        <div class="form-group">
            <label for="inputBudget" class="col-sm-3 control-label">Budget</label>
            <div class="col-sm-9">
                <input type="text" value="{{{$prefilled['budget']}}}"
                       name="budget" class="form-control" id="inputBudget" autocomplete="off" />
            </div>
        </div>
    </div>

</div>


<div class="row">
    <div class="col-lg-12 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save edits to transfer" class="btn btn-default btn-info btn-lg" />
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