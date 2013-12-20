@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('edit'.OBJ,$object))
@section('content')
<div class="row">
  <div class="col-md-4">
    <h3>Edit {{OBJ}} "{{$object->name}}"</h3>

    {{Form::open()}}
    <div class="form-group">
      <label for="inputName">{{ucfirst(OBJ)}} name / identifier</label>
      <input type="text" name="name" class="form-control" id="inputName" placeholder="{{$object->name}}" value="{{$object->name}}"><br />
      <span class="text-danger">{{$errors->first('name')}}</span>
    </div>

    <div class="form-group">
      <label for="inputBalance">Parent {{OBJ}}</label>
      {{Form::select('parent_component_id',$parents,$object->parent_component_id,array('class' => 'form-control'))}}
      
      <span class="text-danger">{{$errors->first('parent_component_id')}}</span>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-default">Save edits to {{OBJ}}</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop