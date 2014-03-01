@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-md-6">
    <h2>Delete {{{$account->name}}}</h2>
    {{Form::open()}}
    <p>
        Are you sure you want to delete this account? All related
        transactions and transfers will be removed. Nine times out of ten,
        it's smarter to just hide it.
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger">Yes,
          delete it</button>
        <a href="{{URL::previous()}}" class="btn btn-default">Never mind</a>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop