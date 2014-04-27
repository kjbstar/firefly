@extends('layouts.default')
@section('breadcrumbs', Breadcrumbs::render('editcomponent',$component))
@section('content')
<div class="row">
  <div class="col-lg-6 col-md-12">
    <h2>Edit {{$component->type->type}} "{{{$component->name}}}"</h2>

      {{Form::open(['class' => 'form-horizontal','files' => true])}}
    <div class="form-group">
      <label for="inputName" class="col-sm-4 control-label">{{ucfirst($component->type->type)}} name</label>
        <div class="col-sm-8">
      <input type="text" name="name" class="form-control" id="inputName"
             placeholder="{{{$component->name}}}" value="{{{$prefilled['name']}}}"><br
            />
      <span class="text-danger">{{$errors->first('name')}}</span>
            </div>
    </div>

    <div class="form-group">
      <label for="inputBalance" class="col-sm-4 control-label">Parent {{$component->type->type}}
          <small>(optional)</small></label>
        <div class="col-sm-8">
      {{Form::select('parent_component_id',$parents,$prefilled['parent_component_id'],
            array('class' => 'form-control'))}}
            <span class="text-danger">{{$errors->first('parent_component_id')}}</span>
            </div>

    </div>


      <!-- REPORTING -->
      <div class="form-group">
          <label for="inputReporting" class="col-sm-4 control-label">Reporting</label>
          <div class="col-sm-8">
              <div class="checkbox">
                  <label>
                      @if($prefilled['reporting'] == 1)
                      <input type="checkbox" name="reporting" value="1" checked="checked">
                      @else
                      <input type="checkbox" name="reporting" value="1">
                      @endif
                      Show this {{$component->type->type}} in reports.
                  </label>
              </div>
          </div>
      </div>

      <!-- ICON FILE -->
      <div class="form-group">
          <label for="inputFile" class="col-sm-4 control-label">Icon</label>
          <div class="col-sm-8">
                      @if($prefilled['hasIcon'])
                      {{$prefilled['iconTag']}}
                      (remove button)
                      @endif
                        <input type="file" id="exampleInputFile" name="icon">
                        <p class="help-block">Only png's accepted, 16x16!</p>
          </div>
      </div>


      <button type="submit" class="btn btn-default">Save edits to {{$component->type->type}}</button>

    {{Form::close()}}

  </div>
</div>


@stop