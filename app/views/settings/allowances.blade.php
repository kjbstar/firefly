@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('allowances'))

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12">
        <h2>Budgeting with allowances</h2>
        <p>
            Although you can give all your <a href="{{URL::Route('beneficiaries')}}">beneficiaries</a>,
            <a href="{{URL::Route('budgets')}}">budgets</a> and <a href="{{URL::Route('categories')}}">categories</a>
            an individual limit (each month too!) you might be like me and simply want to set a "limit" on how
            much you can spend each month.
        </p>
        <p>
            This is very useful for those who have a fixed income or a fixed amount of money they may spend each month.
            Although nothing much happens when you cross this limit, setting it up might help you keep your finances
            in check.
        </p>
        <p>
            There is a default "limit" you can set; each month defaults back to this amount.
            If you have special months, you can set them up individually as well.
        </p>
        <p>
            On the front page, when this value is set, the 'budgeting' tab will appear. The
            <span class="text-info">blue bar</span> will show the month's progress. The
            <span class="text-success">green bar</span> will show the budgeted amount for that month.
            If you spend more money, it will <span class="text-warning">change</span>
            <span class="text-danger">colour</span>.
        </p>
        <p>
            With the introduction of shared accounts and shared finances, it is now also possible to give
            accounts a budget.
        </p>

        <h3>Default montly allowance (for all accounts)</h3>
        <div class="panel panel-default">

            <div class="panel-body">
                {{Form::open()}}
                <p>
                    Enter the default montly allowance you had in mind
                </p>
                <p>
                <div class="input-group">
                    <span class="input-group-addon">&euro;</span>
                    <input type="amount" name="defaultAllowance"
                          class="form-control"
                   value="{{{$defaultAllowance->value > 0 ?
                   $defaultAllowance->value
                    : ''}}}" />
                </div>
                </p>
                <p>
                    <input type="submit" class="btn btn-info" />
                </p>
                {{Form::close()}}
            </div>
        </div>

        <h3>Allowances for specific months and accounts</h3>

        <ul class="list-group">
            @foreach($allowances as $allowance)
            <li class="list-group-item">
                <span class="badge">{{mf($allowance->value,false,false)}}</span>
                For <a href="{{URL::Route('accountoverviewmonth',[$allowance->account_id,$allowance->date->format('Y'),$allowance->date->format('m')])}}">{{{$allowance->account->name}}}</a> in
                {{$allowance->date->format('F Y')}}

                <div class="btn-group pull-right">
                    <a data-toggle="modal" data-target="#PopupModal"
                       href="{{URL::Route('editallowance',
                       $allowance->id)}}" class="btn btn-info btn-default
                       btn-xs"><span
                            class="glyphicon glyphicon-pencil"></span></a>
                    <a data-toggle="modal" data-target="#PopupModal"
                       href="{{URL::Route('deleteallowance',
                       $allowance->id)}}"
                       class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    &nbsp;&nbsp;
                </div>
            </li>
            @endforeach
        </ul>

        <a href="{{URL::Route('addallowance')}}"
           class="btn btn-default btn-info"><span class="glyphicon
           glyphicon-plus-sign"></span> Add an allowance for a particular
            month</a>



    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
    </div>

</div>
@endsection
@section('scripts')
<script src="js/settings.js"></script>
@endsection