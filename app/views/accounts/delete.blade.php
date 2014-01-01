@extends('layouts.default')
@section('content')
<div class="row">
  <div class="col-md-6">
    <h3>Delete {{{$account->name}}}</h3>
    {{Form::open()}}
    <p>
        Are you sure you want to delete this account? All related
        transactions and transfers will be removed. Nine times out of ten,
        it's smarter to just hide it.
    </p>
    <div class="form-group">
      <button type="submit" class="btn btn-danger btn-default">Yes,
          delete it</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop