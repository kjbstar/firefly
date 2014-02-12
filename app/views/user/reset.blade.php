@extends('layouts.user')
@section('content')
<div class="row">
    <div class="col-lg-12">
          <p>
            To reset your password, simply enter your email address here.
          </p>

          {{Form::open(array('class' => 'form-inline'))}}
            <div class="form-group">
              <label class="sr-only" for="inputEmail">Email address</label>
              <input type="email" autocomplete="off" class="form-control" id="inputEmail" name="email" placeholder="Email">
            </div>
            <button type="submit" class="btn btn-primary">Reset me</button>
          {{Form::close()}}
        <ul style="margin-top:40px;">
            <li><a href="{{URL::route('login')}}">... or log in</a></li>
            <li><a href="{{URL::route('register')}}">... or register</a></li>
        </ul>
        </div>
      </div>
@stop