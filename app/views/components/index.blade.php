@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('components',$type))
@section('content')
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <h2>All {{Str::plural($type->type)}}</h2>
    <p>
      <a href="{{URL::Route('addcomponent',$type->id)}}" class="btn btn-default"><span class="glyphicon glyphicon-plus-sign"></span> Add new {{$type->type}}</a>
    </p>

      <table class="table table-bordered table-striped">
          <tr>
              <th>Name</th>
              <th>&nbsp;</th>
          </tr>
          @foreach($components as $c)
          <tr>
              <td>
                  @if($c['hasIcon'])
                    {{$c['iconTag']}}
                  @endif
                  <a href="{{URL::Route('componentoverview',$c['id'])}}">{{{$c['name']}}}</a></td>
              <td>
              <div class="btn-group">
                  <a href="{{URL::Route('editcomponent',$c['id'])}}" class="btn btn-default"><span
                          class="glyphicon glyphicon-pencil"></span></a> <a href="{{URL::Route('deletecomponent',[$c['id']])}}"
                                                                            class="btn btn-default btn-danger"><span
                          class="glyphicon glyphicon-trash"></span></a>
              </div>
              </td>
          </tr>
          <!-- loop the children -->
          @if(count($c['children']) > 0)
          @foreach($c['children'] as $child)
          <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;
                  @if($child['hasIcon'])
                  {{$child['iconTag']}}
                  @endif
                  <a href="{{URL::Route('componentoverview',$child['id'])}}">{{{$child['name']}}}</a>
              </td>
              <td>
                  <div class="btn-group">
                      <a href="{{URL::Route('editcomponent',$child['id'])}}" class="btn btn-default"><span
                              class="glyphicon glyphicon-pencil"></span></a> <a
                          href="{{URL::Route('deletecomponent',$child['id'])}}" class="btn btn-default btn-danger"><span
                              class="glyphicon glyphicon-trash"></span></a>
                  </div>
              </td>
          </tr>
          @endforeach
          @endif

          @endforeach
      </table>

    <p>
      <a href="{{URL::Route('addcomponent',$type->id)}}" class="btn btn-default"><span
          class="glyphicon glyphicon-plus-sign"></span> Add new {{$type->type}}</a>
    </p>
  </div>
</div>
@stop