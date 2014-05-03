@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('predictables'))
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3>All predictables</h3>
        <p class="text-info">
            If you have predictable expenses or incomes you can create "predictables" for them. Firefly will
            try to find matching transactions and mark them as such. The homepage will show them as well, giving you an idea
            of which bills you can expect this month. Several charts will include them as well.
        </p>
        <p>
            <a href="{{URL::Route('addpredictable')}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add predictable</a>
        </p>
        @include('list.predictables-large')
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop