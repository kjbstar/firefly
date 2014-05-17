@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('home'))
@section('content')


<!-- TOP BAR -->
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Searching for "{{{$search['queryText']}}}"</small>
        </h1>
        <p class="text-info">
        @if($search['specials']['afterDate'])
            Searching after date {{$search['specials']['afterDate']->format('d-M-Y')}}.<br />
        @endif
        @if($search['specials']['beforeDate'])
            Searching before date {{$search['specials']['beforeDate']->format('d-M-Y')}}.<br />
        @endif

        </p>
        @if(isset($result['time']))
        <p class="text-warning">
            Cached search results from {{$result['time']->format('dS M, Y @ H:i')}}
        </p>
        @endif

    </div>
</div>


<!-- RESULTS -->
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Transactions <small>Found {{$result['count']['transactions']}} relevant
                @if($result['count']['transactions'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>
        @if(!isset($result['result']['transactions']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['transactions'] > 0 && isset($result['result']['transactions']))
            @include('list.mutations-small',['mutations' => $result['result']['transactions']])
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Transfers <small>Found {{$result['count']['transfers']}} relevant
                @if($result['count']['transfers'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>
        @if(!isset($result['result']['transfers']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['transfers'] > 0 && isset($result['result']['transfers']))
            @include('list.mutations-small',['mutations' => $result['result']['transfers']])
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Beneficiaries <small>Found {{$result['count']['beneficiaries']}} relevant
                @if($result['count']['beneficiaries'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($result['result']['beneficiaries']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['beneficiaries'] > 0 && isset($result['result']['beneficiaries']))
        <table class="table table-condensed table-bordered">
            @foreach($result['result']['beneficiaries'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route('componentoverview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Categories <small>Found {{$result['count']['categories']}} relevant
                @if($result['count']['categories'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($result['result']['categories']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['categories'] > 0 && isset($result['result']['categories']))
        <table class="table table-condensed table-bordered">
            @foreach($result['result']['categories'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route('componentoverview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Budgets <small>Found {{$result['count']['budgets']}} relevant
                @if($result['count']['budgets'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($result['result']['budgets']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['budgets'] > 0 && isset($result['result']['budgets']))
        <table class="table table-condensed table-bordered">
            @foreach($result['result']['budgets'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route('componentoverview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Accounts <small>Found {{$result['count']['accounts']}} relevant
                @if($result['count']['budgets'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($result['result']['accounts']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($result['count']['accounts'] > 0 && isset($result['result']['accounts']))
        <table class="table table-condensed table-bordered table-bordered">
            @foreach($result['result']['accounts'] as $a)
            <tr>
                <td><a href="{{URL::Route('accountoverview',$a->id)}}" title="{{{$a->name}}}">{{{$a->name}}}</a></td>
                <td>{{mf(0,true)}}</td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>


@stop


@section('scripts')
@stop