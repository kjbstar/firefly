@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('add'.OBJ))
@section('content')
<div class="row">
  <div class="col-lg-6  col-md-12">
    <h3>Add a new {{OBJ}}</h3>

      {{Form::open(['class' => 'form-horizontal'])}}

    <div class="form-group">
      <label for="inputName" class="col-sm-4 control-label">{{ucfirst(OBJ)}} name</label>
        <div class="col-sm-8">
      <input type="text" name="name" class="form-control" id="inputName"
             placeholder="{{ucfirst(OBJ)}} Name" value="{{{Input::old('name')
             }}}">
      <span class="text-danger">{{$errors->first('name')}}</span>
            </div>
    </div>

    <div class="form-group">
      <label for="inputParent" class="col-sm-4 control-label">Parent {{OBJ}} <small>(optional)</small>
      </label>
        <div class="col-sm-8">
      {{Form::select('parent_component_id',$parents,0,array('class' => 'form-control'))}}
      <span class="text-danger">{{$errors->first('parent_component_id')}}</span>
            </div>
    </div>

      <button type="submit" class="btn btn-default">Save new {{OBJ}}</button>

    {{Form::close()}}

  </div>
</div>


@stop