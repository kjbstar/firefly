@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-lg-6 col-md-12">
    <h3>Delete {{OBJ}} "{{{$object->name}}}"</h3>

    {{Form::open()}}
    <p>
        Are you sure you want to delete {{OBJ}} "{{{$object->name}}}"?
        Transactions related to this {{OBJ}} will lose this connection.
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger">Delete
          {{{$object->name}}}</button>
        <a href="{{URL::previous()}}" class="btn btn-default">Never mind</a>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop