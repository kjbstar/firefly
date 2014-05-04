@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('profile'))
@section('content')
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h2>Your profile</h2>
        <p class="lead"><a href="{{URL::Route('change-password')}}">Change your password</a></p>
        <p class="lead"><a href="{{URL::Route('change-username')}}">Change your username</a></p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <h3>Some weird stats</h3>
        <table class="table">
            <tr>
                <td>Total money in:</td>
                <td style="width:50%;">{{mf($stats['totalIn'],true)}}</td>
            </tr>
            <tr>
                <td>Total money out:</td>
                <td>{{mf($stats['totalOut'],true)}}</td>
            </tr>

            <tr>
                <td>Average income:</td>
                <td>{{mf($stats['avgIn'],true)}}</td>
            </tr>
            <tr>
                <td>Average expense:</td>
                <td>{{mf($stats['avgOut'],true)}}</td>
            </tr>
            <tr>
                <td>Transferred back and forth</td>
                <td>{{mf($stats['transferred'],true)}}</td>
            </tr>
        </table>

        <table class="table">
            <tr>
                <td>Total transactions</td>
                <td style="width:50%;">
                    <span title="Income">{{$stats['countIn']}}</span> +
                    <span title="Expense">{{$stats['countOut']}}</span>
                    = {{$stats['countOut']+$stats['countIn']}}</td>
            </tr>
            <tr>
                <td>Total transfers</td>
                <td>{{$stats['transfers']}}</td>
            </tr>
            @foreach($stats['types'] as $type => $count)
            <tr>
                <td>Total {{$type}}</td>
                <td>{{$count}}</td>
            </tr>
            @endforeach

        </table>

    </div>
</div>

@stop