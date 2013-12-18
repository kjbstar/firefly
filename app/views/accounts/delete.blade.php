@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-md-4">
    <h3>Delete {{$account->name}}</h3>

    {{Form::open()}}
    <p>
        Are you sure?
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger btn-default">YES</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop