@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render(OBJS))
@section('content')
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <h2>All {{OBJS}}</h2>
    <p>
      <a href="{{URL::Route('add'.OBJ)}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add new {{OBJ}}</a>
    </p>

      <table class="table table-bordered table-striped">
          <tr>
              <th>Name</th>
              <th>&nbsp;</th>
          </tr>
          @foreach($objects as $o)
          <tr>
              <td><a href="{{URL::Route(OBJ.'overview',$o['id'])}}">{{{$o['name']}}}</a></td>
              <td>
              <div class="btn-group">
                  <a href="{{URL::Route('edit'.OBJ,$o['id'])}}" class="btn btn-default"><span
                          class="glyphicon glyphicon-pencil"></span></a> <a href="{{URL::Route('delete'.OBJ,[$o['id']])}}"
                                                                            class="btn btn-default btn-danger"><span
                          class="glyphicon glyphicon-trash"></span></a>
              </div>
              </td>
          </tr>
          <!-- loop the children -->
          @if(count($o['children']) > 0)
          @foreach($o['children'] as $child)
          <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;
                  <a href="{{URL::Route(OBJ.'overview',array($child['id']))}}">{{{$child['name']}}}</a>
              </td>
              <td>
                  <div class="btn-group">
                      <a href="{{URL::Route('edit'.OBJ,[$child['id']])}}" class="btn btn-default"><span
                              class="glyphicon glyphicon-pencil"></span></a> <a
                          href="{{URL::Route('delete'.OBJ,[$child['id']])}}" class="btn btn-default btn-danger"><span
                              class="glyphicon glyphicon-trash"></span></a>
                  </div>
              </td>
          </tr>
          @endforeach
          @endif

          @endforeach
      </table>

    <p>
      <a href="{{URL::Route('add'.OBJ)}}" class="btn btn-default"><span
          class="glyphicon glyphicon-plus-sign"></span> Add new {{OBJ}}</a>
    </p>
  </div>
</div>
@stop