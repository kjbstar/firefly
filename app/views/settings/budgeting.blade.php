@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('budgeting'))

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12">
        <h2>Budgeting</h2>
        <p>
            Although you can give all your <a href="{{URL::Route
            ('beneficiaries')}}">beneficiaries</a>, <a href="{{URL::Route
            ('budgets')}}">budgets</a> and <a href="{{URL::Route
            ('categories')}}">categories</a> an
            individual limit (each month too!) you might be like me and
            simply want to set a "limit" on how much you can spend each month.
        </p>
        <p>
            This is very useful for those who have a fixed income or a fixed
            amount of money they may spend each month. Although nothing much
            happens when you cross this limit, setting it up might help you
            keep your finances in check.
        </p>
        <p>
            There is a default "limit" you can set; each month defaults
            back to this amount. If you have special months,
            you can set them up individually as well.
        </p>
        <p>
            On the front page, when this value is set,
            the 'budgeting' tab will appear. The <span
                class="text-info">blue bar</span> will show the month's
            progress. The <span class="text-success">green bar</span> will
            show the budgeted amount for that month. If you spend more money,
            it will <span class="text-warning">change</span> <span
                class="text-danger">colour</span>.
        </p>


        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Default montly budget</h3>
            </div>
            <div class="panel-body">
                {{Form::open()}}
                <p>
                    Enter the default montly budget you had in mind
                </p>
                <p><input type="amount" name="defaultBudget"
                          class="form-control"
                         placeholder="&euro;"
                   value="{{$defaultBudget->value > 0 ? $defaultBudget->value
                    : ''}}" /></p>
                <p>
                    <input type="submit" class="btn btn-info" />
                </p>
                {{Form::close()}}
            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
    </div>

</div>
@endsection
@section('scripts')

@endsection