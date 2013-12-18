@extends('layouts.empty')
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1 class="text-danger">Inconsistent data</h1>
        <p>
            An error has occured; some data seems to be inconsistent. <a
                href="{{URL::Route('recalc')}}">
                Please
            go here and try again.</a>
        </p>
    </div>

</div>
@endsection
