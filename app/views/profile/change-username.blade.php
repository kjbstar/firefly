@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('change-password'))
@section('content')
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h2>Change your username</h2>
        <p>
            To change your username, fill in this extremely obvious form.
        </p>
        {{Form::open(['class' => 'form-horizontal'])}}

        <div class="form-group">
            <label for="inputUsername" class="col-sm-4 control-label">New username</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="inputUsername" name="username" value="{{{Auth::user()->username}}}" placeholder="{{{Auth::user()->username}}}">
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