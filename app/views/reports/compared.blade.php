@extends('layouts.default')

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Comparing {{$first}} with {{$second}}</small>
        </h1>
    </div>
</div>

@foreach($components as $component)
<div class="row">
    <div class="col-lg-12 col-md-12">
            <h3>{{$component->name}}</h3>
            <div class="chart" id="component-{{$component->id}}"
                 data-id="{{$component->id}}"></div>
        </div>
    </div>

@endforeach

@endsection
@section('scripts')
<script type="text/javascript">
    var first = {{$first}};
    var second = {{$second}};
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/compared.js"></script>
@endsection