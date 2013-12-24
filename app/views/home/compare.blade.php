@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Txt date</th>
                <th>Txt amount</th>
                <th>Txt descr</th>
            </tr>
            @foreach($data as $t)
            <tr>
                <td>{{$t['date']->format('d m Y')}}</td>
                <td>{{$t['otherdate']->format('d m Y')}}</td>
                <td>{{mf($t['amount'])}}</td>
                <td><small>{{$t['descr']}}</small></td>
                @if($t['transaction'] === false)
                <td class="danger">NOT FOUND</td>
                @endif
            </tr>

            @endforeach
        </table>

    </div>

</div>
@endsection
@section('scripts')
@endsection