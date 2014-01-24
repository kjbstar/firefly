@extends('layouts.default')

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Slow...</small>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        @foreach($data as $entry)
            <span style="font-size:{{100 + $entry['size']}}%;">
                {{$entry['count']}}x {{$entry['name']}}
            </span>
        @endforeach
        </div>
    </div>

@foreach($data as $entry)
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3 class="animated slideInLeft animHeader">
            {{$entry['spacing']}}{{$entry['name']}}</h3>
        Given your current pattern of spending {{mf($entry['avgs'])}}
        every {{$entry['avgh']}} hours, it's probable that you will
        go for another {{$entry['count']}} trips this month spending
        another {{mf($entry['amount'])}}. Spent so far is {{mf
        ($entry['sum'])}}
    </div>
</div>
@endforeach

<div id="piechart" style="width: 900px; height: 500px;"></div>




@endsection
@section('styles')
<link rel="stylesheet" href="/css/animate.css" />
@stop
@section('scripts')

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            @foreach($data as $entry)
            ['{{$entry['name']}}',     {{floatval($entry['amount'])}}],
            @endforeach
            ['Left',{{$left}}]
        ]);

        var money = new google.visualization.NumberFormat({decimalSymbol: ',', groupingSymbol: '.', prefix: '€ '});
        money.format(data, 1);

        var options = {
            title: 'Slow?',
            pieHole: 0.6,
            pieSliceText: 'value',
            slices: {
                {{count($data)}}: {color:'transparent'}
    }
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }
    $(document).ready(function () {
        //$('.faded').fadein();
    });
</script>

@endsection