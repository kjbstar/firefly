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
                    <span class="input-group-addon">{{$currency}}</span>
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
        @include('partials.types')

        <!-- ignore in allowance (default is zero) -->
        <div class="form-group">
            <label for="inputIgnoreAllowance" class="col-sm-3 control-label">Allowance</label>
            <div class="col-sm-9">
                <div class="checkbox">
                    <label>
                        @if($prefilled['ignoreallowance'])
                        <input type="checkbox" name="ignoreallowance" value="1" checked="checked" />
                        @else
                        <input type="checkbox" name="ignoreallowance" value="1" />
                        @endif
                        Do not substract this transaction
                        from the allowance (if set).
                    </label>
                </div>
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