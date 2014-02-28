@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render(OBJ,$component,$date))
@section('content')
<div class="row">
    <div class="col-lg-12">
        @if(is_null($date))
        <h3>General overview for {{OBJ}} "{{{$component->name}}}"</h3>
        @else
        <h3>General overview for {{OBJ}} "{{{$component->name}}}" in
            {{$date->format
            ('F Y')}}</h3>
        @endif
        @if($parent)
        <h4>Child of {{{$parent->name}}}</h4>
        @endif
      </div>
  </div>
@if($display == 'months')
<div class="row">
    <div class="col-lg-12">
        <h4>Months</h4>
        <table class="table table-bordered table-striped">
            <tr>
                <th>Month</th>
                <th>Total transactions</th>
                <th colspan="2">Limit</th>
                <th>Total amount</th>
            </tr>
            @foreach($transactions as $m)
            <tr>
                <td><a href="{{$m['url']}}"
                       title="{{$m['month']}}">{{$m['title']}}</a></td>
                <td>{{$m['count']}}</td>
                @if(isset($m['limit']))
                <td>{{mf($m['limit'],false,true)}}</td>
                <td>

                    <div class="btn-group">
                        <a data-toggle="modal" data-target="#LimitModal" href="{{URL::Route('edit'.OBJ.'limit',[$m['limit-id']])}}" class="btn btn-info btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a data-toggle="modal" data-target="#LimitModal" href="{{URL::Route('delete'.OBJ.'limit',[$m['limit-id']])}}" class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>

                </td>

                @else
                <td colspan="2"><a data-toggle="modal" href="{{URL::Route('add'.OBJ.'limit',[$component->id,$m['year'],$m['month']])}}" data-target="#LimitModal" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus-sign"></span></a></td>
                @endif

                @if(isset($m['limit']) && ($m['sum']*-1) > $m['limit'])
                <td class="danger">{{mf($m['sum'],false,true)}}</td>
                @else
                <td>{{mf($m['sum'],false,true)}}</td>
                @endif


            </tr>
            @endforeach
        </table>
    </div>
</div>
@endif
@if($display == 'transactions')
<div class="row">
  <div class="col-lg-12">
    <h4>Transactions</h4>

    @include('list.transactions')
  </div>
</div>
@endif
@stop
@section('scripts')
<script type="text/javascript">
    var object = "{{OBJ}}";
    var objects = "{{OBJS}}";
    var id = {{$component->id}};
  @if(isset($date))
      var month = {{intval($date->format('m'))}};
    var year = {{intval($date->format('Y'))}};
    @else
    var month = null;
    var year = null;
    @endif

</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/meta.js"></script>
@stop
@section('styles')
@stop