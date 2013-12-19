@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Category</th>
                <th>Budget</th>
            </tr>
            @foreach($list as $t)
            <tr>
                <td>#{{$t->id}}</td>
                <td>{{$t->date->format('d F Y')}}</td>
                <td>{{mf($t->amount,true)}}</td>
                <td><a href="{{URL::Route('edittransaction',
                $t->id)}}">{{$t->description}}</a></td>
                <td>{{$t->cat}}</td>
                <td>{{$t->bud}}</td>
            </tr>

            @endforeach
        </table>

    </div>

</div>
@endsection
@section('scripts')
@endsection