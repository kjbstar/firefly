@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('add'.OBJ))
@section('content')
<div class="row">
  <div class="col-md-4">
    <h3>Add a new {{OBJ}}</h3>

    {{Form::open()}}

    <div class="form-group">
      <label for="inputName">{{ucfirst(OBJ)}} name / identifier</label>
      <input type="text" name="name" class="form-control" id="inputName"
             placeholder="{{ucfirst(OBJ)}} Name" value="{{{Input::old('name')
             }}}"><br />
      <span class="text-danger">{{$errors->first('name')}}</span>
    </div>

    <div class="form-group">
      <label for="inputParent">Parent {{OBJ}}</label>
      {{Form::select('parent_component_id',$parents,0,array('class' => 'form-control'))}}
      <span class="text-danger">{{$errors->first('parent_component_id')}}</span>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-default">Save new {{OBJ}}</button>
    </div>

    {{Form::close()}}

  </div>
</div>


@stop