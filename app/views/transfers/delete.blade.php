@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-lg-8">
    <h3>Delete "{{{$transfer->description}}}"</h3>

    {{Form::open()}}
    <p>
        Are you sure you want to delete "{{{$transfer->description}}}" with an
        amount of {{mf($transfer->amount,true)}}?
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger">YES</button>
        <a href="{{URL::previous()}}" class="btn btn-default">Never mind</a>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop