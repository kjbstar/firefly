@extends('layouts.user')
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">

{{Form::open(array('class' => 'form-inline'))}}
<div class="form-group">
    <label class="sr-only" for="inputEmail">Email address</label>
    <input type="email" autocomplete="off" class="form-control" id="inputEmail"
           name="email" placeholder="Email">
</div>
<div class="form-group">
    <label class="sr-only" for="inputPassword">Password</label>
    <input type="password" class="form-control" id="inputPassword"
           name="password" placeholder="Password">
</div>
<button type="submit" class="btn btn-primary">Sign in</button>
{{Form::close()}}
<ul style="margin-top:40px;">
    <li><a href="{{URL::route('register')}}">... or register</a></li>
    <li><a href="{{URL::route('reset')}}">... or reset your password</a></li>
</ul>
    </div>
</div>
@stop