@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addtransaction'))
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>
            Add a new transaction
        </h3>

    </div>
</div>

{{Form::open(['class' => 'form-horizontal'])}}
<div class="row">

    <div class="col-lg-6">
        <h4>Mandatory fields</h4>

        <!-- description -->
        @if($errors->has('description'))
        <div class="form-group has-error">
        @else
        <div class="form-group">
        @endif
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
                    <input type="number" value="{{{$prefilled['amount']}}}" name="amount" step="any"
                           class="form-control" id="inputAmount">
                </div>
                @if($errors->has('amount'))
                <span class="text-danger">{{$errors->first('amount')
                    }}</span><br />
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
                <span class="text-danger">{{$errors->first('date')
                    }}</span><br />
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
                {{Form::select('account_id',$accounts,$prefilled['account_id'],
                ['class' => 'form-control'])}}
                @if($errors->has('account_id'))
                <span class="text-danger">{{$errors->first('account_id')
                    }}</span><br />
                @endif
            </div>
        </div>


    </div>
    <div class="col-lg-6">
        <h4>Optional fields</h4>
        @if(Type::count() > 0)
        <p class="text-info">
            Use the suggestions given in the {{Type::count()}} field(s) below,
            or add something new by simply typing it in. You can find your new entries
            under the relevant "Lists"-menu item in the top menu bar.
        </p>
        @endif
        @include('partials.types')
        <!-- ignore in transactions (default is zero) -->
        <div class="form-group">
            <label for="inputIgnore" class="col-sm-3 control-label">Ignore</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        @if($prefilled['ignoreprediction'])
                            <input type="checkbox" name="ignoreprediction" value="1" checked="checked" />
                        @else
                            <input type="checkbox" name="ignoreprediction" value="1" />
                    @endif
                        Ignores this transaction in predictions.
                    </label>
                </div>
            </div>
        </div>

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

        <!-- mark in charts -->
        <div class="form-group">
            <label for="inputMark" class="col-sm-3 control-label">Mark</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        @if($prefilled['mark'])
                        <input type="checkbox" name="mark" value="1" checked="checked" />
                        @else
                        <input type="checkbox" name="mark" value="1" />
                        @endif
                    Marks
                    this transaction in certain charts.
                    </label>
                    </div>
            </div>
        </div>



    </div>

</div>


<div class="row">
    <div class="col-lg-3 col-lg-offset-0" style="margin-top:20px;">
        <input type="submit" name="submit" value="Save new transaction" class="btn btn-default btn-info btn-lg" />
    </div>

</div>

{{Form::close()}}
@stop
@section('scripts')
<script src="js/typeahead.min.js"></script>
    <script type="text/javascript">


        $( document ).ready(function() {

            @foreach(Type::allTypes() as $type)
            $('input[name="{{$type->type}}"]').typeahead({
                name: '{{$type->type}}_{{Auth::user()->id}}',
                prefetch: 'home/type/{{$type->id}}/typeahead',
                limit: 10
            });
            @endforeach


        });
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
@section('styles')
@stop