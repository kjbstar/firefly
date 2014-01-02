@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('reports'))

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Reports</small>
        </h1>
        <p>
            @foreach($years as $year)
            <a href="{{URL::Route('yearreport',$year)}}" class="btn
            btn-default btn-lg">{{$year}}</a>

            @endforeach
        </p>
    </div>
</div>


@endsection
@section('scripts')
@endsection