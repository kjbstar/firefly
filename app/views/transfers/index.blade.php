@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3>All transfers</h3>
        {{$transfers->links()}}
        @include('list.transfers')
        {{$transfers->links()}}
    </div>
</div>  


@stop
@section('scripts')
@stop
@section('styles')
@stop