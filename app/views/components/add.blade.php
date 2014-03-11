@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('add'.OBJ))
@section('content')
<div class="row">
  <div class="col-lg-6  col-md-12">
    <h2>Add a new {{OBJ}}</h2>

      {{Form::open(['class' => 'form-horizontal'])}}

      <!-- NAME -->
    <div class="form-group">
      <label for="inputName" class="col-sm-4 control-label">{{ucfirst(OBJ)}} name</label>
        <div class="col-sm-8">
      <input type="text" name="name" class="form-control" id="inputName"
             placeholder="{{ucfirst(OBJ)}} Name" value="{{{Input::old('name')
             }}}">
      <span class="text-danger">{{$errors->first('name')}}</span>
            </div>
    </div>

      <!-- PARENT -->
    <div class="form-group">
      <label for="inputParent" class="col-sm-4 control-label help-popover"
             title="Set a parent {{OBJ}}">Parent
          {{OBJ}}
          <small>(optional)</small>
      </label>
        <div class="col-sm-8">
      {{Form::select('parent_component_id',$parents,0,array('class' => 'form-control'))}}
      <span class="text-danger">{{$errors->first('parent_component_id')}}</span>
            </div>
    </div>

      <!-- mark in charts -->
      <div class="form-group">
          <label for="inputReporting" class="col-sm-4 control-label">Reporting</label>
          <div class="col-sm-8">
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="reporting" value="1">
                      Show this {{OBJ}} in reports.
                  </label>
              </div>
          </div>
      </div>

      <button type="submit" class="btn btn-default">Save new {{OBJ}}</button>

    {{Form::close()}}

  </div>
</div>


@stop