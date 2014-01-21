@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render(OBJS))
@section('content')
<div class="row">
  <div class="col-lg-12">
    <h3>All {{OBJS}}</h3>

    <p>
      <div class="btn-group">
      <a href="{{URL::Route('add'.OBJ)}}" class="btn btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Add
        new {{OBJ}}</a>
      <a href="{{URL::Route('empty'.OBJ)}}" class="btn btn-default">Transactions without a {{OBJ}}</a>
    </div>
    </p>
  </div>
</div>
<div class="row">
  <div class="col-lg-6 col-md-6">


    <ul class="list-group">
      @foreach($objects as $o)

      <li class="list-group-item">
        <span class="badge">{{$o['count']}}</span>
        <a href="{{URL::Route(OBJ.'overview',
        array($o['id']))}}">{{{$o['name']}}}</a>

        <div class="btn-group pull-right">
          <a href="{{URL::Route('edit'.OBJ,[$o['id']])}}" class="btn btn-default btn-xs"><span
              class="glyphicon glyphicon-pencil"></span></a> <a href="{{URL::Route('delete'.OBJ,[$o['id']])}}"
                                                                class="btn btn-default btn-danger btn-xs"><span
              class="glyphicon glyphicon-trash"></span></a>&nbsp;&nbsp;
        </div>
      </li>

      <!-- get childs -->
      @if(count($o['children']) > 0)
      @foreach($o['children'] as $child)
      <li class="list-group-item">
        <span class="badge">{{$child['count']}}</span>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="{{URL::Route(OBJ.'overview',array($child['id']))
        }}">{{{$child['name']}}}</a>

        <div class="btn-group pull-right">
          <a href="{{URL::Route('edit'.OBJ,[$child['id']])}}" class="btn btn-default btn-xs"><span
              class="glyphicon glyphicon-pencil"></span></a> <a
            href="{{URL::Route('delete'.OBJ,[$child['id']])}}" class="btn btn-default btn-danger btn-xs"><span
              class="glyphicon glyphicon-trash"></span></a>&nbsp;&nbsp;
        </div>
      </li>
      @endforeach
      @endif

      @endforeach
    </ul>
    <p>
      <a href="{{URL::Route('add'.OBJ)}}" class="btn btn-info"><span
          class="glyphicon glyphicon-plus-sign"></span> Add new {{OBJ}}</a>
    </p>
  </div>
  <div class="col-lg-6 col-md-6">
    <div id="object-avg-chart">

        </div>
      <p><small>Only {{OBJS}} with more than 5 transactions are
              counted.</small></p>
  </div>
</div>
@stop
@section('scripts')
@section('scripts')
<script type="text/javascript">
    var object = "{{OBJ}}";
    var objects = "{{OBJS}}";
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="js/meta.js"></script>
@stop
@section('styles')
@stop