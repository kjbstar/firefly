@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('piggyselect'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Piggy banks</small>
        </h1>
        <p>
            Piggy banks are a way of managing saved money. Instead of
            budgeting, which you set in advance and then spend money on,
            piggy banks are saving up until a pre-set amount,
            after which you're free to do with the money as you please.
        </p>
        <p>
            To get started with piggy banks, select the account on which the
            piggy banks should apply.
        </p>
        </div>
    </div>
<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        {{Form::open()}}
            <div class="form-group">
                <label for="inputAccount">Account</label>
                {{Form::select('account',$accounts,null,
                ['class' => 'form-control','id' => 'inputAccount'])}}
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        {{Form::close()}}
    </div>
</div>


@endsection
@section('scripts')
@endsection