@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('home'))
@section('content')


<!-- TOP BAR -->
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h1>Firefly
            <small>Searching for "{{{$queryText}}}"</small>
        </h1>
        @if(count($specials) > 0)
        <p class="text-info">
        @if($specials['afterDate'])
            Searching after date {{$specials['afterDate']->format('d-M-Y')}}.<br />
        @endif
        @if($specials['beforeDate'])
            Searching before date {{$specials['beforeDate']->format('d-M-Y')}}.<br />
        @endif

        </p>
        @endif
        @if(!is_null($time))
        <p class="text-warning">
            Cached search results from {{$time->format('dS M, Y @ H:i')}}
        </p>
        @endif

    </div>
</div>


<!-- RESULTS -->
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Transactions <small>Found {{$counts['transactions']}} relevant
                @if($counts['transactions'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>
        @if(!isset($results['transactions']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['transactions'] > 0 && isset($results['transactions']))
        <table class="table table-condensed table-striped table-bordered">
            @foreach($results['transactions'] as $t)
            <tr>
                <td>{{$t->date->format('d-m-Y')}}</td>
                <td>

                    @if($t->beneficiary && $t->beneficiary->hasIcon())
                    {{$t->beneficiary->iconTag()}}
                    @endif

                    @if($t->category && $t->category->hasIcon())
                    {{$t->category->iconTag()}}
                    @endif


                    @if($t->budget && $t->budget->hasIcon())
                    {{$t->budget->iconTag()}}
                    @endif

                    <a href="{{URL::Route('edittransaction',[$t->id])}}">{{{$t->description}}}</a>

                </td>
                <td>{{mf($t->amount,true)}}</td>
            </tr>
            @endforeach

        </table>
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Transfers <small>Found {{$counts['transfers']}} relevant
                @if($counts['transfers'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>
        @if(!isset($results['transfers']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['transfers'] > 0 && isset($results['transfers']))
        <table class="table table-condensed table-bordered">
            @foreach($results['transfers'] as $t)
            <tr>
                <td>{{$t->date->format('d-m-Y')}}</td>
                <td><a href="{{URL::Route('edittransfer',[$t->id])}}">{{{$t->description}}}</a></td>
                <td>{{mf($t->amount,true)}}</td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Beneficiaries <small>Found {{$counts['beneficiaries']}} relevant
                @if($counts['beneficiaries'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($results['beneficiaries']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['beneficiaries'] > 0 && isset($results['beneficiaries']))
        <table class="table table-condensed table-bordered">
            @foreach($results['beneficiaries'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route($b->type.'overview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Categories <small>Found {{$counts['categories']}} relevant
                @if($counts['categories'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($results['categories']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['categories'] > 0 && isset($results['categories']))
        <table class="table table-condensed table-bordered">
            @foreach($results['categories'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route($b->type.'overview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <h2>Budgets <small>Found {{$counts['budgets']}} relevant
                @if($counts['budgets'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($results['budgets']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['budgets'] > 0 && isset($results['budgets']))
        <table class="table table-condensed table-bordered">
            @foreach($results['budgets'] as $b)
            <tr>
                <td>{{$b->iconTag()}}</td>
                <td><a href="{{URL::Route($b->type.'overview',$b->id)}}">{{{$b->name}}}</a>
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="col-lg-6 col-md-6">
        <h2>Accounts <small>Found {{$counts['accounts']}} relevant
                @if($counts['budgets'] == 1)
                entry
                @else
                entries
                @endif
            </small></h2>

        @if(!isset($results['accounts']))
        <p class="text-info">
            Too many results. Please narrow your search.
        </p>
        @endif
        @if($counts['accounts'] > 0 && isset($results['accounts']))
        <table class="table table-condensed table-bordered table-bordered">
            @foreach($results['accounts'] as $a)
            <tr>
                <td><a href="{{URL::Route('accountoverview',$a->id)}}" title="{{{$a->name}}}">{{{$a->name}}}</a></td>
                <td>{{mf($a->balanceOnDate(new Carbon\Carbon),true)}}</td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</div>


@stop


@section('scripts')
@stop