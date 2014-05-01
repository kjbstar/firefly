@extends('layouts.user')
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">

{{Form::open(array('class' => 'form-inline'))}}
<div class="form-group">
    <label class="sr-only" for="inputUsername">Username</label>
    <input type="text" autocomplete="off" class="form-control" id="inputUsername"
           name="username" placeholder="Username">
</div>
<div class="form-group">
    <label class="sr-only" for="inputPassword">Password</label>
    <input type="password" class="form-control" id="inputPassword"
           name="password" placeholder="Password">
</div>
<button type="submit" class="btn btn-primary">Sign in</button>
{{Form::close()}}
<ul style="margin-top:40px;">
    @if(Config::get('firefly.allowRegistration'))
        <li><a href="{{URL::route('register')}}">... or register</a></li>
    @endif
    <li><a href="{{URL::route('reset')}}">... or reset your password</a></li>
</ul>
    </div>
</div>
@stop