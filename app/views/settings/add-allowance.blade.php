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
              <input type="month" name="date" class="form-control"
                     id="inputAmount" />
                  </div>
          </div>
        <button type="submit" class="btn btn-primary">Save
            allowance</button>
    </div>
  </div>
@stop