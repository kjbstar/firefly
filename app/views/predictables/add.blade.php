@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addpredictable'))
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
                       value="{{{Input::old('description') ? Input::old('description') : $transaction ? $transaction->description : ''}}}"
                       id="inputDescription" placeholder="Description">
                @if($errors->has('description'))
                <span class="text-danger">{{$errors->first('description')
                    }}</span><br />
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
                    <input type="number" value="{{Input::old('amount') ? Input::old('amount') : $transaction ? $transaction->amount : 0}}" name="amount" step="any" class="form-control" id="inputAmount">
                </div>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')
                    }}</span><br />
                @endif

            </div>
        </div>

        <!-- LEEWAY -->
        <div class="form-group
             @if($errors->has('date'))
             has-error
             @endif
             ">
            <label for="inputLeeway" class="col-sm-3 control-label">Leeway</label>
            <div class="col-sm-3">
                <div id="lowAmount" style="text-align:right;"></div>

            </div>
            <div class="col-sm-3">
                <div id="slider"></div>

            </div>
            <div class="col-sm-3">
                <div id="highAmount"></div>
            </div>
            <input type="hidden" name="pct" value="10" id="inputLeeway" />
        </div>

        <!-- the day of month -->
        <div class="form-group
             @if($errors->has('dom'))
             has-error
             @endif
             ">
            <label for="inputDom" class="col-sm-3 control-label">Day of month</label>
            <div class="col-sm-9">
                <input type="number" min="1" max="31" value="{{Input::old('dom') ? Input::old('dom') : $transaction ? $transaction->date->format('j') : 0}}" name="dom" step="any" class="form-control" id="inputDom">
                @if($errors->has('dom'))
                <span class="text-danger">{{$errors->first('dom')
                    }}</span><br />
                @endif
            </div>
        </div>


    </div>
    <div class="col-lg-6">
        <h4>Optional fields</h4>

        <!-- beneficiary (can be created) --> 
        <div class="form-group">
            <label for="inputBeneficiary" class="col-sm-3 control-label">Required beneficiary</label>
            <div class="col-sm-9">
                {{Form::select('beneficiary_id',$components['beneficiary'],Input::old('beneficiary_id') ? Input::old('beneficiary_id') : $transaction && $transaction->beneficiary_id ? $transaction->beneficiary_id : null,['class' => 'form-control'])}}
            </div>
        </div>
        
        <!-- category (can be created) -->
        <div class="form-group">
            <label for="inputCategory" class="col-sm-3 control-label">Required category</label>
            <div class="col-sm-9">
                {{Form::select('category_id',$components['category'],Input::old('category_id') ? Input::old('category_id') : $transaction && $transaction->category_id ? $transaction->category_id : null,['class' => 'form-control'])}}

            </div>
        </div>

        <!-- budget (can be created) --> 
        <div class="form-group">
            <label for="inputBudget" class="col-sm-3 control-label">Required budget</label>
            <div class="col-sm-9">
                {{Form::select('budget_id',$components['budget'],Input::old('budget_id') ? Input::old('budget_id') : $transaction && $transaction->budget_id ? $transaction->budget_id : null,['class' => 'form-control'])}}
            </div>
        </div>


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
<script src="jqueryui/js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript">
    var pct = 10; // TODO does not prop over failed commits.
</script>
<script src="js/predictables.js"></script>
@stop
@section('styles')
<link href="css/typeahead.js-bootstrap.css" rel="stylesheet" media="screen">
<link href="jqueryui/css/ui-lightness/jquery-ui-1.10.4.custom.css" rel="stylesheet" media="screen">
<link href="css/transactions.css" rel="stylesheet" media="screen">
@stop