@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('change-password'))
@section('content')
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h3>Change your password</h3>
        <p>
            To change your password, fill in this extremely obvious form.
        </p>
        {{Form::open(['class' => 'form-horizontal'])}}

        <div class="form-group">
            <label for="inputCurrentPassword" class="col-sm-4 control-label">Current password</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="inputCurrentPassword" name="current" placeholder="Current password">
            </div>
        </div>

        <div class="form-group">
            <label for="inputNewPassword" class="col-sm-4 control-label">New password</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="inputNewPassword" name="new" placeholder="New password">
            </div>
        </div>

        <div class="form-group">
            <label for="inputNewPasswordAgain" class="col-sm-4 control-label">New password (again)</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="inputNewPasswordAgain" name="newagain" placeholder="New password (again)">
            </div>
        </div>

        <input type="submit" class="btn btn-default" name="submit" value="Change!" />


        {{Form::close()}}
    </div>
</div>

@stop
@section('scripts')
@stop
@section('styles')
@stop