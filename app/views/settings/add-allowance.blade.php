@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('addallowance'))

@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>Add a new allowance</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12"

    <p>
        Set a specific allowance for a month of your choice.
    </p>

    {{Form::open(['class' => 'form-horizontal','role' => 'form'])}}

    <div class="form-group">
        <label class="col-sm-4 control-label"
               for="inputAmount">Account (optional)</label>

        <div class="col-sm-8">
            {{Form::select('account_id',$accounts,null,['class' => 'form-control'])}}
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label"
               for="inputAmount">Amount</label>

        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon">&euro;</span>
                <input type="number" step="any" name="amount" class="form-control" id="inputAmount">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="inputDate" class="col-sm-4
              control-label">Date</label>

        <div class="col-sm-8">
            <input type="month" name="date" class="form-control monthPicker"
                   id="inputAmount "/>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save
        allowance
    </button>
</div>
</div>
@stop
@section('scripts')

<script type="text/javascript">
    yepnope({
        test : Modernizr.inputtypes.month,
        nope : ['//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js','http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.css'],
        complete: function () {
            $(".monthPicker").datepicker({
                dateFormat: 'mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,

                onClose: function (dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
                }
            });

            $(".monthPicker").focus(function () {
                $(".ui-datepicker-calendar").hide();
                $("#ui-datepicker-div").position({
                    my: "center top",
                    at: "center bottom",
                    of: $(this)
                });
            });
        }
    });



</script>
@stop