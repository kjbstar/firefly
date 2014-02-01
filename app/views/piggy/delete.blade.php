@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-md-6">
    <h3>Delete {{{$piggy->name}}}</h3>
    {{Form::open()}}
    <p>
        Are you sure you want to delete this piggy bank?
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger btn-default">Yes,
          delete it</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop