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
                       value="{{{$prefilled['description']}}}"
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
                    <input type="number" value="{{{$prefilled['amount']}}}" name="amount" step="any" class="form-control" id="inputAmount">
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
            <input type="hidden" name="pct" value="{{$prefilled['pct']}}" id="inputLeeway" />
        </div>

        <!-- Account -->
        <div class="form-group
             @if($errors->has('account_id'))
             has-error
             @endif
             ">
            <label for="inputAccount" class="col-sm-3 control-label">Account</label>
            <div class="col-sm-9">
                {{Form::select('account_id',$accounts,$prefilled['account_id'],
                ['class' => 'form-control'])}}
                @if($errors->has('account_id'))
                <span class="text-danger">{{$errors->first('account_id')
                    }}</span><br />
                @endif
            </div>
        </div>

        <!-- the day of month -->
        <div class="form-group
             @if($errors->has('dom'))
             has-error
             @endif
             ">
            <label for="inputDom" class="col-sm-3 control-label">Day of month</label>
            <div class="col-sm-9">
                <input type="number" min="1" max="31" value="{{$prefilled['dom']}}" name="dom" step="any" class="form-control" id="inputDom">
                @if($errors->has('dom'))
                <span class="text-danger">{{$errors->first('dom')
                    }}</span><br />
                @endif
            </div>
        </div>


    </div>
    <div class="col-lg-6">
        <h4>Optional fields</h4>

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
        <!-- inactive predictable (default is zero) -->
        <div class="form-group">
            <label for="inputInactive" class="col-sm-3 control-label">Inactive</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        @if($prefilled['inactive'] == true)
                        <input type="checkbox" name="inactive" checked="checked" value="1">
                        @else
                        <input type="checkbox" name="inactive" value="1">
                        @endif
                        This predictable should not actually catch transactions.
                    </label>
                </div>
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
    $( document ).ready(function() {

        @foreach(Type::allTypes() as $type)
        $('input[name="{{$type->type}}"]').typeahead({
            name: '{{$type->type}}_{{Auth::user()->id}}',
            prefetch: 'home/type/{{$type->id}}/typeahead',
            limit: 10
        });
            @endforeach

    });
</script>
<script src="js/predictables.js"></script>
@stop
@section('styles')
<link href="css/typeahead.js-bootstrap.css" rel="stylesheet" media="screen">
<link href="jqueryui/css/ui-lightness/jquery-ui-1.10.4.custom.css" rel="stylesheet" media="screen">
<link href="css/transactions.css" rel="stylesheet" media="screen">
@stop