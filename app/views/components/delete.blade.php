@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-lg-6 col-md-12">
    <h2>Delete {{$component->type->type}} "{{{$component->name}}}"</h2>

    {{Form::open()}}
    <p>
        Are you sure you want to delete {{$component->type->type}} "{{{$component->name}}}"?
        @if($transactions > 0 || $transfers > 0)
        There are {{$transactions}} transaction(s) and {{$transfers}} transfer(s) related to this {{$component->type->type}}
        that will lose this connection.
        @endif
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger">Delete
          {{{$component->name}}}</button>
        <a href="{{URL::previous()}}" class="btn btn-default">Never mind</a>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop