@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addtransaction'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Add a new transaction
            @if($account)
            to {{{$account->name}}}
            @endif
        </h3>
        @if($count == 0)
        <div class="alert alert-info">
            <p>
                <strong>Add your first transaction</strong>
            </p>
            <p>
                Transactions are usually expenses paid (with a debit or
                credit card). They can also be incomes such as salary.
            </p>
            <p>
                The fields on the right are "free fields"; once you've
                entered something they'll auto-suggest it next time.
            </p>
        </div>
        @endif

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
                       value="{{{Input::old('description')}}}"
                       id="inputDescription" placeholder="Description">
                @if($errors->has('description'))
                <span class="text-danger">{{$errors->first('description')
                    }}</span><br />
                @endif
                @if($count == 0)
                <span class="text-info">What best describes the
                    transaction? Avoid using the store's name or general
                    descriptions such as "groceries".
                </span>
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
                    <input type="number" value="{{Input::old('amount')}}" name="amount" step="any" class="form-control" id="inputAmount">
                </div>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')
                    }}</span><br />
                @endif
                @if($count == 0)
                <span class="text-info">
                    If the transaction is an expense, enter a negative amount.
                    Otherwise, enter a positive amount.
                </span>
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
                <input type="date" name="date" value="{{Input::old('date') ? Input::old('date') : date('Y-m-d')}}"  class="form-control" id="inputDate">
                @if($errors->has('date'))
                <span class="text-danger">{{$errors->first('date')
                    }}</span><br />
                @endif
                @if($count == 0)
                <span class="text-info">Enter the date this transaction
                    occured. This date cannot be before the date the
                    account (below) was opened.</span>
                @endif
            </div>
        </div>

        <!-- accounts -->
        <div class="form-group
             @if($errors->has('account_id'))
             has-error
             @endif
             ">
            <label for="inputAccount" class="col-sm-3 control-label">Account</label>
            <div class="col-sm-9">
                {{Form::select('account_id',$accounts,$id ? $id : Input::old('account_id'),['class' => 'form-control'])}}
                @if($errors->has('date'))
                <span class="text-danger">{{$errors->first('account_id')
                    }}</span><br />
                @endif
                @if($count == 0)
                <span class="text-info">Select the account this
                    transaction is relevant to.</span>
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
                <input type="text" value="{{{Input::old('beneficiary')}}}"
                       name="beneficiary" class="form-control" id="inputBeneficiary" autocomplete="off" />
                <br />
                @if($count == 0)
                <span class="text-info">
                    Enter the beneficiary, such as the store's name in case
                    of an expense or the company who paid you.
                </span>
                @endif
            </div>
        </div>
        
        <!-- category (can be created) -->
        <div class="form-group">
            <label for="inputCategory" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9">
                <input type="text" value="{{{Input::old('category')}}}"
                       name="category" class="form-control"
                       id="inputCategory" autocomplete="off" /><br />
                @if($count == 0)
                <span class="text-info">
                    Under which category is this transaction best filed?
                    Think "house" or "personal".
                </span>
                @endif
            </div>
        </div>

        <!-- budget (can be created) --> 
        <div class="form-group">
            <label for="inputBudget" class="col-sm-3 control-label">Budget</label>
            <div class="col-sm-9">
                <input type="text" value="{{{Input::old('budget')}}}"
                       name="budget" class="form-control" id="inputBudget"
                       autocomplete="off" /><br />
                @if($count == 0)
                <span class="text-info">
                    If you're budgeting, use this free field.
                </span>
                @endif
            </div>
        </div>

        <!-- ignore in transactions (default is zero) -->
        <div class="form-group">
            <label for="inputIgnore" class="col-sm-3 control-label">Ignore</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                <input type="checkbox" name="ignore" value="1"> Ignores this transaction in predictions.
                    </label>
                </div>
                @if($count == 0)
                <span class="text-info">
                    Large (one time) transactions can skew the predictions.
                    So, use this check to make sure your predictions stay
                    accurate.
                </span>
                @endif
            </div>
        </div>
        
        <!-- mark in charts -->
        <div class="form-group">
            <label for="inputMark" class="col-sm-3 control-label">Mark</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                <input type="checkbox" name="mark" value="1">Marks
                    this transaction in certain charts.
                    </label>
                @if($count == 0)

                    <br />
                <span class="text-info">
                    Like it says.
                </span>
                @endif
                    </div>
            </div>
        </div>



    </div>

</div>


<div class="row">
    <div class="col-lg-3 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save new transaction" class="btn btn-default btn-info btn-lg" />
    </div>
    <div class="col-lg-9 col-lg-offset-0" style="margin-top:20px;">
        <label><input name="another" value="1" type="checkbox"> Add another
            transaction</label>
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